@extends('layouts.admin')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Modifier l'expédition #{{ $shipment->id }}</h1>
        <p class="admin-page-subtitle">
            @if($shipment->pointDeVente)
                {{ $shipment->pointDeVente->name }}
            @else
                Expédition
            @endif
        </p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.dashboard') }}" class="btn-admin secondary">
            <i class="fa-solid fa-chart-line"></i> Retour au tableau de bord
        </a>
        <a href="{{ route('admin.shipments.index') }}" class="btn-admin secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<form method="POST" action="{{ route('admin.shipments.update', $shipment) }}" class="card admin-form" id="shipment-form">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="point_de_vente_id">Point de vente *</label>
        <select name="point_de_vente_id" id="point_de_vente_id" required>
            @foreach($pointsDeVente as $pdv)
                <option value="{{ $pdv->id }}" {{ old('point_de_vente_id', $shipment->point_de_vente_id) == $pdv->id ? 'selected' : '' }}>
                    {{ $pdv->display_name }}
                </option>
            @endforeach
        </select>
    </div>

    @php
        $marinade = $products->firstWhere('name', 'Parasel-Bio Marinade');
        $marinadeId = $marinade ? (int) $marinade->id : 0;
        $marinadeVariants = $marinade && is_array($marinade->variants ?? null) ? $marinade->variants : [];
    @endphp
    <div class="form-group">
        <label>Produits et quantités *</label>
        <p class="form-hint">Pour Parasel-Bio Marinade, choisissez la variante (poids).</p>
        <div class="shipment-products-card">
            <div id="shipment-items">
            @foreach($shipment->items as $index => $item)
                @php
                    $pid = old('items.'.$index.'.product_id') ?? $item->product_id;
                    $showVariant = (int) $pid === $marinadeId;
                    $variantVal = old('items.'.$index.'.variant_size') ?? $item->variant_size;
                @endphp
                <div class="shipment-item-row flex gap align-center flex-wrap" style="margin-bottom: 12px;">
                    <select name="items[{{ $index }}][product_id]" required class="item-product" data-row="{{ $index }}">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ $pid == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <div class="item-variant-wrap" data-row="{{ $index }}" style="display: {{ $showVariant ? 'block' : 'none' }};">
                        <select name="items[{{ $index }}][variant_size]" class="item-variant" title="Variante (obligatoire pour Marinade)">
                            <option value="">Variante</option>
                            @foreach($marinadeVariants as $v)
                                <option value="{{ $v['size'] ?? '' }}" {{ $variantVal === ($v['size'] ?? '') ? 'selected' : '' }}>{{ $v['size'] ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="number" name="items[{{ $index }}][quantity]" min="1" value="{{ old('items.'.$index.'.quantity', $item->quantity) }}" placeholder="Qté" style="width: 80px;" required>
                    <button type="button" class="btn-admin secondary btn-remove-row" title="Supprimer la ligne"><i class="fa-solid fa-minus"></i></button>
                </div>
            @endforeach
            @if($shipment->items->isEmpty())
                <div class="shipment-item-row flex gap align-center flex-wrap" style="margin-bottom: 12px;">
                    <select name="items[0][product_id]" required class="item-product" data-row="0">
                        <option value="">Produit</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <div class="item-variant-wrap" data-row="0" style="display: none;">
                        <select name="items[0][variant_size]" class="item-variant" title="Variante (obligatoire pour Marinade)">
                            <option value="">Variante</option>
                            @foreach($marinadeVariants as $v)
                                <option value="{{ $v['size'] ?? '' }}">{{ $v['size'] ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="number" name="items[0][quantity]" min="1" value="1" placeholder="Qté" style="width: 80px;" required>
                    <button type="button" class="btn-admin secondary btn-remove-row"><i class="fa-solid fa-minus"></i></button>
                </div>
            @endif
            </div>
        </div>
        <div class="shipment-products-footer">
            <div id="shipment-total" style="font-size:14px;color:#111827;">
                Total : <strong id="shipment-total-value">0 FCFA</strong>
            </div>
            <button type="button" id="add-item-row" class="btn-admin secondary btn-icon-only" title="Ajouter un produit">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>

    <div class="form-group">
        <label for="status">Statut *</label>
        <select name="status" id="status" required>
            @foreach(\App\Models\Shipment::STATUSES as $value => $label)
                <option value="{{ $value }}" {{ old('status', $shipment->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="shipped_at">Date d'expédition *</label>
        <input type="date" name="shipped_at" id="shipped_at" value="{{ old('shipped_at', $shipment->shipped_at?->format('Y-m-d')) }}" required>
    </div>
    <div class="form-group">
        <label for="delivery_method">Mode de livraison</label>
        <input type="text" name="delivery_method" id="delivery_method" value="{{ old('delivery_method', $shipment->delivery_method) }}">
    </div>
    <div class="form-group">
        <label for="tracking_reference">Référence de suivi *</label>
        <input type="text" name="tracking_reference" id="tracking_reference" value="{{ old('tracking_reference', $shipment->tracking_reference) }}" required>
    </div>
    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea name="notes" id="notes" rows="2">{{ old('notes', $shipment->notes) }}</textarea>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-admin success"><i class="fa-solid fa-check"></i> Enregistrer</button>
        <a href="{{ route('admin.shipments.index') }}" class="btn-admin secondary">Annuler</a>
    </div>
</form>

@php
    $productsForJs = $products->map(function ($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'price' => (float) $p->price,
        ];
    })->values();
    $marinadeForJs = $marinade ? ['id' => $marinade->id, 'variants' => array_map(function ($v) {
        return [
            'size' => $v['size'] ?? '',
            'price' => isset($v['price']) ? (float) $v['price'] : null,
        ];
    }, $marinadeVariants)] : null;
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('shipment-items');
    const addBtn = document.getElementById('add-item-row');
    const products = @json($productsForJs);
    const marinade = @json($marinadeForJs);
    const marinadeId = marinade ? marinade.id : 0;
    const totalEl = document.getElementById('shipment-total-value');

    function toggleVariant(rowEl) {
        const sel = rowEl.querySelector('.item-product');
        const wrap = rowEl.querySelector('.item-variant-wrap');
        const variantSel = rowEl.querySelector('.item-variant');
        if (!wrap || !variantSel) return;
        if (parseInt(sel.value, 10) === marinadeId) {
            wrap.style.display = 'block';
            variantSel.required = true;
        } else {
            wrap.style.display = 'none';
            variantSel.required = false;
            variantSel.value = '';
        }
    }

    function getPriceForRow(rowEl) {
        const productSelect = rowEl.querySelector('.item-product');
        const variantSelect = rowEl.querySelector('.item-variant');
        const productId = parseInt(productSelect.value || '0', 10);
        let basePrice = 0;

        // Si Marinade sans variante choisie, on ne compte pas encore de prix
        if (marinade && productId === marinadeId && (!variantSelect || !variantSelect.value)) {
            return 0;
        }
        products.forEach(function(p) {
            if (p.id === productId) basePrice = p.price || 0;
        });
        if (marinade && productId === marinadeId && variantSelect && variantSelect.value) {
            let variantPrice = null;
            if (marinade.variants) {
                marinade.variants.forEach(function(v) {
                    if (v.size === variantSelect.value && v.price != null) {
                        variantPrice = v.price;
                    }
                });
            }
            if (variantPrice != null) {
                return variantPrice;
            }
        }
        return basePrice;
    }

    function updateTotal() {
        if (!totalEl) return;
        let total = 0;
        const rows = container.querySelectorAll('.shipment-item-row');
        rows.forEach(function(r) {
            const qtyInput = r.querySelector('input[type="number"]');
            const productSelect = r.querySelector('.item-product');
            if (!qtyInput || !productSelect || !productSelect.value) return;
            const qty = parseInt(qtyInput.value || '0', 10);
            if (qty <= 0) return;
            const price = getPriceForRow(r);
            total += price * qty;
        });
        const formatted = new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(Math.round(total));
        totalEl.textContent = formatted + ' FCFA';
    }

    function addRow(index) {
        const row = document.createElement('div');
        row.className = 'shipment-item-row flex gap align-center flex-wrap';
        row.style.marginBottom = '12px';
        let options = '<option value="">Produit</option>' + products.map(function(p) { return '<option value="' + p.id + '">' + p.name + '</option>'; }).join('');
        let variantOpts = '<option value="">Variante</option>';
        if (marinade && marinade.variants) {
            marinade.variants.forEach(function(v) {
                if (v.size) variantOpts += '<option value="' + v.size + '">' + v.size + '</option>';
            });
        }
        row.innerHTML = '<select name="items[' + index + '][product_id]" required class="item-product" data-row="' + index + '">' + options + '</select>' +
            '<div class="item-variant-wrap" data-row="' + index + '" style="display:none;"><select name="items[' + index + '][variant_size]" class="item-variant" title="Variante (obligatoire pour Marinade)">' + variantOpts + '</select></div>' +
            '<input type="number" name="items[' + index + '][quantity]" min="1" value="1" placeholder="Qté" style="width:80px;" required>' +
            '<button type="button" class="btn-admin secondary btn-remove-row" title="Supprimer la ligne"><i class="fa-solid fa-minus"></i></button>';
        container.appendChild(row);
        row.querySelector('.item-product').addEventListener('change', function() {
            toggleVariant(row);
            updateTotal();
        });
        const qtyInput = row.querySelector('input[type="number"]');
        qtyInput.addEventListener('input', updateTotal);
        const variantSelect = row.querySelector('.item-variant');
        if (variantSelect) {
            variantSelect.addEventListener('change', updateTotal);
        }
        toggleVariant(row);
        row.querySelector('.btn-remove-row').addEventListener('click', function() {
            if (container.querySelectorAll('.shipment-item-row').length > 1) {
                row.remove();
                updateTotal();
            }
        });
    }

    container.querySelectorAll('.shipment-item-row').forEach(function(row) {
        const sel = row.querySelector('.item-product');
        const qtyInput = row.querySelector('input[type="number"]');
        const variantSelect = row.querySelector('.item-variant');
        if (sel) {
            sel.addEventListener('change', function() {
                toggleVariant(row);
                updateTotal();
            });
            toggleVariant(row);
        }
        if (qtyInput) {
            qtyInput.addEventListener('input', updateTotal);
        }
        if (variantSelect) {
            variantSelect.addEventListener('change', updateTotal);
        }
    });

    addBtn.addEventListener('click', function() {
        const index = container.querySelectorAll('.shipment-item-row').length;
        addRow(index);
        updateTotal();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-row')) {
            const row = e.target.closest('.shipment-item-row');
            if (container.querySelectorAll('.shipment-item-row').length > 1) {
                row.remove();
                updateTotal();
            }
        }
    });

    updateTotal();
});
</script>
@endsection
