<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SalesReportController extends Controller
{
    private function parsePeriod(Request $request): array
    {
        $period = $request->get('period', 'month');
        $customStart = $request->get('date_from');
        $customEnd = $request->get('date_to');

        if ($period === 'custom' && $customStart && $customEnd) {
            $start = Carbon::parse($customStart)->startOfDay();
            $end = Carbon::parse($customEnd)->endOfDay();
            return [$start, $end];
        }

        $now = Carbon::now();
        switch ($period) {
            case 'day':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }
        return [$start, $end];
    }

    public function index(Request $request): View
    {
        [$start, $end] = $this->parsePeriod($request);
        $period = $request->get('period', 'month');
        $dateFrom = $request->get('date_from', $start->format('Y-m-d'));
        $dateTo = $request->get('date_to', $end->format('Y-m-d'));

        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);
        $totalOrders = (clone $ordersQuery)->count();
        $revenue = (clone $ordersQuery)->whereNotIn('status', ['annulee'])->sum('total');
        $delivered = (clone $ordersQuery)->whereIn('status', ['livree_payee', 'livree'])->count();
        $cancelled = (clone $ordersQuery)->where('status', 'annulee')->count();

        $topProducts = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereNotIn('orders.status', ['annulee'])
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('SUM(order_items.line_total) as total_amount'))
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->with('product:id,name')
            ->get();

        return view('admin.reports.sales', compact(
            'totalOrders', 'revenue', 'delivered', 'cancelled', 'topProducts',
            'start', 'end', 'period', 'dateFrom', 'dateTo'
        ));
    }

    public function compare(Request $request): View
    {
        $period1Start = $request->get('period1_from') ? Carbon::parse($request->get('period1_from'))->startOfDay() : Carbon::now()->subMonth()->startOfMonth();
        $period1End = $request->get('period1_to') ? Carbon::parse($request->get('period1_to'))->endOfDay() : Carbon::now()->subMonth()->endOfMonth();
        $period2Start = $request->get('period2_from') ? Carbon::parse($request->get('period2_from'))->startOfDay() : Carbon::now()->startOfMonth();
        $period2End = $request->get('period2_to') ? Carbon::parse($request->get('period2_to'))->endOfDay() : Carbon::now()->endOfMonth();

        $orders1 = Order::whereBetween('created_at', [$period1Start, $period1End]);
        $orders2 = Order::whereBetween('created_at', [$period2Start, $period2End]);

        $count1 = (clone $orders1)->count();
        $count2 = (clone $orders2)->count();
        $revenue1 = (clone $orders1)->whereNotIn('status', ['annulee'])->sum('total');
        $revenue2 = (clone $orders2)->whereNotIn('status', ['annulee'])->sum('total');

        $orderVariation = $count2 - $count1;
        $orderGrowthRate = $count1 > 0 ? (($count2 - $count1) / $count1) * 100 : ($count2 > 0 ? 100 : 0);
        $revenueVariation = $revenue2 - $revenue1;
        $revenueGrowthRate = $revenue1 > 0 ? (($revenue2 - $revenue1) / $revenue1) * 100 : ($revenue2 > 0 ? 100 : 0);

        return view('admin.reports.compare', compact(
            'period1Start', 'period1End', 'period2Start', 'period2End',
            'count1', 'count2', 'revenue1', 'revenue2',
            'orderVariation', 'orderGrowthRate', 'revenueVariation', 'revenueGrowthRate'
        ));
    }

    /**
     * Export du rapport de ventes en PDF.
     * Nécessite le package barryvdh/laravel-dompdf (composer install).
     */
    public function exportPdf(Request $request): Response|RedirectResponse
    {
        [$start, $end] = $this->parsePeriod($request);
        $period = $request->get('period', 'month');

        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);
        $totalOrders = (clone $ordersQuery)->count();
        $revenue = (clone $ordersQuery)->whereNotIn('status', ['annulee'])->sum('total');
        $delivered = (clone $ordersQuery)->whereIn('status', ['livree_payee', 'livree'])->count();
        $cancelled = (clone $ordersQuery)->where('status', 'annulee')->count();

        $topProductsRows = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start, $end])
            ->whereNotIn('orders.status', ['annulee'])
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_quantity'), DB::raw('SUM(order_items.line_total) as total_amount'))
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $productIds = $topProductsRows->pluck('product_id')->unique()->filter()->values()->all();
        $productNames = $productIds ? Product::whereIn('id', $productIds)->pluck('name', 'id')->all() : [];
        $topProducts = $topProductsRows->map(function ($row) use ($productNames) {
            return (object) [
                'product_name' => $productNames[$row->product_id] ?? 'Produit #' . $row->product_id,
                'total_quantity' => $row->total_quantity,
                'total_amount' => $row->total_amount ?? 0,
            ];
        });

        $filename = 'rapport-ventes-' . $start->format('Y-m-d') . '-' . $end->format('Y-m-d') . '.pdf';

        try {
            $pdf = app('dompdf.wrapper');
            return $pdf->loadView('admin.reports.pdf', compact(
                'totalOrders', 'revenue', 'delivered', 'cancelled', 'topProducts', 'start', 'end', 'period'
            ))
                ->setPaper('a4', 'portrait')
                ->download($filename);
        } catch (Throwable $e) {
            Log::error('Export PDF échoué: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $message = config('app.debug')
                ? 'Export PDF : ' . $e->getMessage() . ' (fichier ' . basename($e->getFile()) . ', ligne ' . $e->getLine() . ')'
                : 'Export PDF indisponible. Vérifiez les logs ou activez APP_DEBUG pour voir l\'erreur.';

            if (request()->expectsJson()) {
                return response()->json(['message' => $message], 503);
            }
            return redirect()
                ->route('admin.reports.index', $request->query())
                ->with('error', $message);
        }
    }
}
