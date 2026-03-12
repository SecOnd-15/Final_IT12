@extends('layouts.app')
@section('title', 'POS - ATIN')
@push('styles')
<style>
    .pos-container {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 30px;
        min-height: calc(100vh - 60px);
        padding: 10px;
    }
    
    .items-section {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--card-shadow);
        display: flex;
        flex-direction: column;
    }
    
    .summary-section {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: var(--card-shadow);
        display: flex;
        flex-direction: column;
        height: min-content;
        position: sticky;
        top: 30px;
    }
    
    .search-input {
        border-radius: 12px !important;
        border: 1px solid #d1d5db !important;
        font-size: 1.1rem !important;
        height: 60px !important;
        padding: 0 20px !important;
    }
    
    .items-list {
        flex: 1;
        max-height: 500px;
        overflow-y: auto;
        margin: 20px 0;
        scrollbar-width: thin;
    }
    
    .item-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .header-row {
        background: #f9fafb;
        border-radius: 8px;
        padding: 12px 16px !important;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
    }
    
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .quantity-btn {
        width: 30px;
        height: 30px;
        border: 1px solid #ddd;
        background: #f8f9fa;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .remove-btn {
        color: #9ca3af;
        cursor: pointer;
        transition: color 0.2s;
        font-size: 1.1rem;
    }
    
    .remove-btn:hover {
        color: #ef4444;
    }
    
    .payment-section {
        margin-top: auto;
    }
    
    .payment-method {
        margin: 10px 0;
    }
    
    .payment-field {
        margin: 10px 0;
    }
    
    .change-display {
        color: #28a745;
        font-weight: bold;
        margin: 10px 0;
    }

    .qty-input {
        width: 60px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 4px 8px;
        text-align: center;
        font-weight: 500;
    }
    .summary-card {
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
        border: 1px solid #f3f4f6;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.925rem;
        margin-bottom: 10px;
        color: #374151;
    }

    .summary-row:last-child {
        margin-bottom: 0;
    }

    .summary-row input {
        max-width: 150px;
    }

    .total-display {
        font-size: 2rem;
        font-weight: 800;
        color: #10b981;
        letter-spacing: -1px;
    }

    .total-label {
        font-size: 1.1rem;
        font-weight: 600;
        color: #111827;
    }

    .payment-method .form-check {
        flex: 1;
        margin: 0;
        padding: 0;
    }

    .payment-method .form-check-input {
        display: none;
    }

    .payment-method .form-check-label {
        display: block;
        padding: 10px;
        text-align: center;
        background: #f3f4f6;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: 2px solid transparent;
    }

    .payment-method .form-check-input:checked + .form-check-label {
        background: rgba(6, 68, 138, 0.05);
        color: var(--congress-blue);
        border-color: var(--congress-blue);
    }

    /* Add to your existing styles */
.select2-container .select2-selection--single {
    height: 60px !important; /* Match your search input height */
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 58px !important;
    font-size: 18px !important; /* Match your search input font size */
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 58px !important;
}

.select2-container--default .select2-selection--single {
    border: 1px solid #dee2e6 !important;
    border-radius: 0.375rem !important;
}

.select2-results__option {
    padding: 10px !important;
}

.select2-search__field {
    font-size: 18px !important; /* Match search font size */
}

/* Highlight low/out of stock products */
.option-out-of-stock {
    background-color: #ffe6e6 !important;
    color: #dc3545 !important;
}

.option-low-stock {
    background-color: #fff3cd !important;
    color: #856404 !important;
}

.option-no-price {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    font-style: italic;
}

/* Add to your existing styles */
.select2-product-option {
    padding: 8px;
    border-bottom: 1px solid #eee;
}

.select2-product-option:last-child {
    border-bottom: none;
}

.select2-product-option .text-muted {
    font-size: 12px;
}

.select2-product-option .badge {
    font-size: 11px;
    padding: 3px 6px;
}

/* Stock badge colors */
.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

/* Fix Select2 dropdown width */
.select2-container--open .select2-dropdown {
    min-width: 400px !important;
    max-width: 600px !important;
}

/* Make Select2 results scrollable */
.select2-results {
    max-height: 300px !important;
    overflow-y: auto !important;
}

/* Light gray background with dark text - most readable */
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #f8f9fa !important;  /* Light gray */
    color: #212529 !important;             /* Dark text */
    border-left: 4px solid #007bff !important; /* Blue accent border */
}

.select2-container--default .select2-results__option--highlighted {
    background-color: #e9ecef !important;  /* Slightly darker gray for hover */
    color: #212529 !important;
}
</style>
@endpush

@section('content')
<div class="pos-container" data-cashier-name="{{ session('user_full_name') ?? session('user_name') ?? 'Cashier' }}">
    <!-- Items Section -->
    <div class="items-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0" style="color: var(--congress-blue);">Checkout</h4>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                <i class="bi bi-cart3 me-1"></i> New Transaction
            </span>
        </div>
        
        <!-- Product Search -->
        <div class="mb-4">
            <select class="form-control search-input" id="productSearch" autofocus>
                <!-- Options will be loaded dynamically -->
            </select>
            <div id="searchError" class="text-danger mt-2 small" style="display: none;"></div>
        </div>

        <!-- Table Header -->
        <div class="item-row header-row mb-2">
            <div style="flex:2">Product</div>
            <div style="flex:1; text-align:center">Quantity</div>
            <div style="flex:1; text-align:right">Price</div>
            <div style="flex:1; text-align:right">Total</div>
            <div style="flex:0.5"></div>
        </div>
        
        <!-- Items List -->
        <div class="items-list" id="itemsList">
            <div class="text-center text-muted py-5">
                <i class="bi bi-cart-x fs-1 opacity-25 d-block mb-3"></i>
                No items added yet
            </div>
        </div>
    </div>
    
    <!-- Summary Section -->
    <div class="summary-section">
        <h4 class="fw-bold mb-4" style="color: var(--congress-blue);">Order Summary</h4>
    
        <!-- Transaction Info -->
        <div class="summary-card mb-4">
            <div class="summary-row">
                <span class="text-secondary">Reference:</span>
                <span class="fw-bold">#{{ now()->format('Ymd') }}-POS</span>
            </div>
            <div class="summary-row">
                <span class="text-secondary">Cashier:</span>
                <span id="cashierName" class="fw-semibold">{{ session('user_full_name') ?? session('user_name') ?? 'Cashier' }}</span>
            </div>
            <div class="summary-row">
                <span class="text-secondary">Date & Time:</span>
                <span id="currentTime" class="small">{{ now()->format('M d, Y h:i A') }}</span>
            </div>
        </div>
    
        <!-- Customer Info -->
        <div class="mb-4">
            <label class="form-label d-flex align-items-center">
                <i class="bi bi-person me-2"></i> Customer Details
            </label>
            <input type="text" id="customerName" class="form-control mb-2" placeholder="Customer Name (Optional)">
            <input type="tel" id="customerContact" class="form-control" placeholder="Contact No. (Optional)">
        </div>
    
        <!-- Totals -->
        <div class="summary-card mb-4" style="background: rgba(6, 68, 138, 0.02);">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotalDisplay">₱0.00</span>
            </div>
            <div class="summary-row">
                <span>VAT (12%)</span>
                <span id="vatDisplay">₱0.00</span>
            </div>
            <div class="pt-3 mt-2 border-top">
                <div class="d-flex justify-content-between align-items-end">
                    <span class="total-label mb-1">Total Amount</span>
                    <span class="total-display" id="totalDisplay">₱0.00</span>
                </div>
            </div>
        </div>
    
        <!-- Payment -->
        <div class="mb-4">
            <label class="form-label mb-3 d-flex align-items-center">
                <i class="bi bi-credit-card me-2"></i> Payment Method
            </label>
            <div class="payment-method d-flex gap-2 mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="payCash" value="Cash" checked>
                    <label class="form-check-label" for="payCash">Cash</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="payGCash" value="GCash">
                    <label class="form-check-label" for="payGCash">GCash</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="payCard" value="Card">
                    <label class="form-check-label" for="payCard">Bank Card</label>
                </div>
            </div>
    
            <div id="cashFields" class="payment-field">
                <label class="form-label">Amount Tendered</label>
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-end-0">₱</span>
                    <input type="number" id="amountTendered" class="form-control border-start-0 ps-0" step="0.01" min="0" placeholder="0.00">
                    <button type="button" id="exactBtn" class="btn btn-outline-primary fw-bold">Exact</button>
                </div>
                <div id="changeDisplay" class="alert alert-success mt-3 py-2 px-3 fw-bold border-0 text-center" style="display:none; border-radius:10px;"></div>
            </div>
    
            <div id="digitalFields" class="payment-field" style="display:none;">
                <label class="form-label">Reference Number</label>
                <input type="text" id="referenceNo" class="form-control form-control-lg shadow-sm" placeholder="Enter Ref #">
            </div>
        </div>
    
        <!-- Actions -->
        <div class="d-grid gap-2">
            <button class="btn btn-primary btn-lg py-3 shadow-primary" id="completeSale" disabled>
                <i class="bi bi-check2-circle me-2"></i> Complete Checkout
            </button>
            <button class="btn btn-light btn-lg py-2 border-0" id="cancelSale" style="background: #fdf2f2; color: #ef4444;">
                Cancel Transaction
            </button>
        </div>
    </div>

    </div>
    
</div>

<!-- Sale Success Modal -->
<div class="modal fade" id="saleSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>
                    Sale Completed
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-2">The sale was completed successfully.</p>
                <p class="fw-bold mb-3">
                    Sale ID: <span id="successSaleId"></span>
                </p>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg" onclick="printReceipt(document.getElementById('successSaleId').textContent)">
                        <i class="bi bi-printer me-1"></i> Print Receipt
                    </button>

                    <a href="{{ route('pos.my-transactions') }}"
                    class="btn btn-outline-secondary">
                        View Recent Transactions
                    </a>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Continue Selling
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    class POSSystem {
        constructor() {
            this.items = JSON.parse(localStorage.getItem('posItems')) || [];
            this.total = 0;
            this.init();
            this.renderItems();
            this.updateTotal();
            this.restorePaymentMethod();
        }

        init() {
            this.setupEventListeners();
            this.startClock();
            this.initSelect2();
        }

        startClock() {
            const timeEl = document.getElementById('currentTime');
            setInterval(() => {
                const now = new Date();
                timeEl.textContent = now.toLocaleString('en-US', {
                    month: 'short',
                    day: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }, 1000);
        }

        initSelect2() {
            const self = this; // Store reference to 'this'
            
            $('#productSearch').select2({
                placeholder: "Enter Barcode, SKU, Model...",
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    url: '/pos/search-product',
                    type: 'POST',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            search_term: params.term,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    processResults: function (data) {
                        if (!data.success) {
                            console.error('Search error:', data.message);
                            return { results: [] };
                        }

                        // Get IDs of products already in the cart
                        const cartProductIds = self.items.map(item => item.product.id);
                        
                        // Format results for Select2, excluding already-added products
                        const results = data.products
                            .filter(product => !cartProductIds.includes(product.id))
                            .map(product => ({
                                id: product.id,
                                text: product.text,
                                name: product.name,
                                model: product.model,
                                sku: product.sku,
                                barcode: product.barcode,
                                stock: product.stock,
                                price: product.price,
                                has_price: product.has_price,
                                stock_status: product.stock_status
                            }));
                        
                        return {
                            results: results,
                            pagination: {
                                more: false
                            }
                        };
                    },
                    cache: false
                },
                templateResult: this.formatProductOption.bind(this),
                templateSelection: this.formatProductSelection.bind(this)
            }).on('select2:select', (e) => {
                this.handleProductSelect(e.params.data);
            }).on('select2:open', () => {
                // Focus the search field inside Select2
                setTimeout(() => {
                    $('.select2-search__field').focus();
                }, 100);
            });
        }

        formatProductOption(product) {
            if (!product.id) {
                return product.text;
            }
            
            // Determine stock badge class
            let stockBadgeClass = '';
            if (product.stock_status === 'out_of_stock') {
                stockBadgeClass = 'badge bg-danger';
            } else if (product.stock_status === 'low_stock') {
                stockBadgeClass = 'badge bg-warning';
            } else {
                stockBadgeClass = 'badge bg-success';
            }
            
            return $(`
                <div class="select2-product-option">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${product.name}</strong>
                             <div class="text-muted small">
                                SKU: ${product.sku}
                                ${product.model ? ` | Model: ${product.model}` : ''}
                                ${product.barcode ? ` | Barcode: ${product.barcode}` : ''}
                            </div>
                        </div>
                        <div class="text-end">
                            <div>Stock: <span class="${stockBadgeClass}">${product.stock}</span></div>
                            <div>${product.has_price ? `₱${parseFloat(product.price).toFixed(2)}` : '<span class="text-danger">No Price</span>'}</div>
                        </div>
                    </div>
                </div>
            `);
        }

        formatProductSelection(product) {
            if (!product.id) {
                return product.text;
            }
            
            // Show a shorter version in the selection box
            let text = `${product.name} [${product.sku}]`;
            if (product.model) {
                text += ` (${product.model})`;
            }
            
            if (!product.has_price) {
                text += ' - No Price';
            }
            
            return text;
        }

        handleProductSelect(productData) {
            const errorDiv = document.getElementById('searchError');
            errorDiv.style.display = 'none';
            
            // Check if product has a price
            if (!productData.has_price) {
                errorDiv.textContent = 'Product has no price set. Please add a price in Product Management.';
                errorDiv.style.display = 'block';
                $('#productSearch').val(null).trigger('change');
                return;
            }

            // Check if product is out of stock
            if (productData.stock <= 0) {
                errorDiv.textContent = 'Product out of stock';
                errorDiv.style.display = 'block';
                $('#productSearch').val(null).trigger('change');
                return;
            }

            // Add product to cart
            this.addProductToCart({
                id: productData.id,
                name: productData.name,
                model: productData.model,
                sku: productData.sku,
                manufacturer_barcode: productData.barcode,
                quantity_in_stock: productData.stock,
                latest_product_price: {
                    retail_price: productData.price
                }
            });

            // Clear the selection
            $('#productSearch').val(null).trigger('change');
        }

        addProductToCart(product) {
            const existingIndex = this.items.findIndex(item => item.product.id === product.id);
            if (existingIndex !== -1) {
                // Check if adding more would exceed stock
                const currentQty = this.items[existingIndex].quantity_sold;
                if (currentQty + 1 > product.quantity_in_stock) {
                    alert(`Cannot exceed available stock (${product.quantity_in_stock} remaining)`);
                    return;
                }
                this.items[existingIndex].quantity_sold++;
            } else {
                this.items.push({
                    product: product,
                    quantity_sold: 1,
                    unit_price: parseFloat(product.latest_product_price.retail_price || 0)
                });
            }

            this.renderItems();
            this.updateTotal();
        }

        renderItems() {
            const itemsList = document.getElementById('itemsList');
            let html = '';
            if (this.items.length === 0) {
                html += '<div class="text-center text-muted py-4">No items added yet</div>';
            } else {
                html += this.items.map((item, index) => `
                    <div class="item-row" style="display:flex; align-items:center; padding:5px 0; border-bottom:1px solid #eee;">
                        <div style="flex:2; min-width: 0;">
                            <div style="font-weight: bold; word-break: break-word; margin-bottom: 4px;">
                                ${item.product.name}
                            </div>
                            <div style="font-size: 13px; color: #6c757d; margin-bottom: 2px;">
                                <span style="font-weight: 500;">SKU:</span> ${item.product.sku || 'N/A'}
                                ${item.product.model ? `<br><span style="font-weight: 500;">Model:</span> <span style="word-break: break-word;">${item.product.model}</span>` : ''}
                            </div>
                            <div style="font-size: 12px; color: #6c757d;">
                                <span style="font-weight: 500;">Stock:</span> ${item.product.quantity_in_stock}
                                ${item.product.manufacturer_barcode ? `<br><span style="font-weight: 500;">Barcode:</span> ${item.product.manufacturer_barcode}` : ''}
                            </div>
                        </div>
                        <div style="flex:1; text-align:center">
                            <input type="number" class="qty-input" min="1" step="1" value="${item.quantity_sold}" onchange="pos.setQuantity(${index}, this.value)">
                        </div>
                        <div style="flex:1; text-align:right">₱${item.unit_price.toFixed(2)}</div>
                        <div style="flex:1; text-align:right">₱${(item.unit_price * item.quantity_sold).toFixed(2)}</div>
                        <div style="flex:0.5; text-align:center">
                            <span class="remove-btn" onclick="pos.removeItem(${index})"><i class="bi bi-trash"></i></span>
                        </div>
                    </div>
                `).join('');
            }

            itemsList.innerHTML = html;
            localStorage.setItem('posItems', JSON.stringify(this.items));
        }

        updateTotal() {
            this.total = this.items.reduce((sum, item) => sum + item.unit_price * item.quantity_sold, 0);
            const subtotal = this.total / 1.12; 
            const vat = this.total - subtotal;  

            document.getElementById('subtotalDisplay').textContent = `₱${subtotal.toFixed(2)}`;
            document.getElementById('vatDisplay').textContent = `₱${vat.toFixed(2)}`;
            document.getElementById('totalDisplay').textContent = `₱${this.total.toFixed(2)}`;
            document.getElementById('digitalAmountInfo').textContent = `Amount: ₱${this.total.toFixed(2)}`;
            this.calculateChange();
            this.updateCompleteButton();
        }

        restorePaymentMethod() {
            const savedMethod = localStorage.getItem('posPaymentMethod') || 'Cash';
            const radioButton = document.querySelector(`input[name="paymentMethod"][value="${savedMethod}"]`);
            if (radioButton) {
                radioButton.checked = true;
                this.handlePaymentMethodChange(savedMethod);
            } else {
                document.querySelector('input[name="paymentMethod"][value="Cash"]').checked = true;
                this.handlePaymentMethodChange('Cash');
            }
        }

        setQuantity(index, value) {
            const qty = parseInt(value);
            const maxStock = this.items[index].product.quantity_in_stock;

            if (isNaN(qty) || qty < 1) {
                return alert("Quantity must be at least 1");
            }

            if (qty > maxStock) {
                alert(`Cannot sell more than ${maxStock} in stock`);
                this.items[index].quantity_sold = maxStock;
            } else {
                this.items[index].quantity_sold = qty;
            }

            this.renderItems();
            this.updateTotal();
        }

        removeItem(index) {
            this.items.splice(index, 1);
            this.renderItems();
            this.updateTotal();
        }

        setupEventListeners() {
            // Payment method change
            document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
                radio.addEventListener('change', (e) => this.handlePaymentMethodChange(e.target.value));
            });

            // Amount tendered input
            document.getElementById('amountTendered').addEventListener('input', () => {
                this.calculateChange();
                this.updateCompleteButton();
            });

            // Exact amount button
            document.getElementById('exactBtn').addEventListener('click', () => {
                this.setExactAmount();
            });

            // Reference number input
            document.getElementById('referenceNo').addEventListener('input', () => {
                this.updateCompleteButton();
            });

            // Complete sale button
            document.getElementById('completeSale').addEventListener('click', () => this.processPayment());

            // Cancel sale button
            document.getElementById('cancelSale').addEventListener('click', () => this.cancelSale());
        }

        handlePaymentMethodChange(method) {
            document.getElementById('cashFields').style.display = method === 'Cash' ? 'block' : 'none';
            document.getElementById('digitalFields').style.display = method === 'Cash' ? 'none' : 'block';
            document.getElementById('amountTendered').value = '';
            document.getElementById('referenceNo').value = '';
            
            // Save payment method to localStorage
            localStorage.setItem('posPaymentMethod', method);
            
            this.updateCompleteButton();
        }

        setExactAmount() {
            const amountTenderedInput = document.getElementById('amountTendered');
            amountTenderedInput.value = this.total.toFixed(2);
            this.calculateChange();
            this.updateCompleteButton();
        }

        calculateChange() {
            const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
            const change = tendered - this.total;
            const display = document.getElementById('changeDisplay');
            
            if (tendered >= this.total && change > 0) {
                display.textContent = `Change: ₱${change.toFixed(2)}`;
                display.style.display = 'block';
                display.style.color = '#28a745';
            } else if (tendered < this.total && tendered > 0) {
                display.textContent = `Amount Insufficient (Short: ₱${Math.abs(change).toFixed(2)})`;
                display.style.display = 'block';
                display.style.color = '#dc3545'; 
            } else {
                display.style.display = 'none';
            }
        }

        updateCompleteButton() {
            const btn = document.getElementById('completeSale');
            const cancelBtn = document.getElementById('cancelSale');
            const method = document.querySelector('input[name="paymentMethod"]:checked').value;
            const tendered = parseFloat(document.getElementById('amountTendered').value) || 0;
            const refNo = document.getElementById('referenceNo').value;

            let valid = this.items.length > 0;

            if (method === 'Cash') {
                valid = valid && tendered >= this.total;
            } else {
                valid = valid && refNo.trim() !== '';
            }

            btn.disabled = !valid;
            cancelBtn.disabled = this.items.length === 0;
        }

        async processPayment() {
            if (this.items.length === 0) return alert("No items in cart!");

            if (!confirm("Are you sure you want to complete this sale?")) {
                return;
            }

            const method = document.querySelector('input[name="paymentMethod"]:checked').value;
            const tendered = parseFloat(document.getElementById('amountTendered').value) || this.total;
            const refNo = document.getElementById('referenceNo').value;
            const customerName = document.getElementById('customerName').value;
            const customerContact = document.getElementById('customerContact').value;

            try {
                const res = await fetch('/pos/complete-sale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: this.items,
                        payment_method: method,
                        amount_tendered: tendered,
                        reference_no: refNo,
                        customer_name: customerName,
                        customer_contact: customerContact
                    })
                });

                const text = await res.text();
                console.log(text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch(e) {
                    console.error('JSON parse error:', e, text);
                    return;
                }
                if (!data.success) throw new Error(data.message);

                // Show success modal
                document.getElementById('successSaleId').textContent = data.sale.id;
                const modal = new bootstrap.Modal(document.getElementById('saleSuccessModal'));
                modal.show();

                // Reset cart AFTER showing success
                this.resetCart();

            } catch (err) {
                alert("Error: " + err.message);
            }
        }

        cancelSale() {
            if (!this.items.length) return;

            if (!confirm("Are you sure you want to cancel this sale? All items will be removed.")) return;

            this.resetCart();
        }

        resetCart() {
            this.items = [];
            this.total = 0;
            localStorage.removeItem('posItems');
            document.querySelector('input[name="paymentMethod"][value="Cash"]').checked = true;
            this.handlePaymentMethodChange('Cash');
            document.getElementById('customerName').value = '';
            document.getElementById('customerContact').value = '';
            document.getElementById('amountTendered').value = '';
            document.getElementById('referenceNo').value = '';
            document.getElementById('changeDisplay').style.display = 'none';
            this.renderItems();
            this.updateTotal();
        }
    }

    let pos;
    document.addEventListener('DOMContentLoaded', () => { 
        pos = new POSSystem(); 
    });

    function printReceipt(id) {
        const url = "{{ route('receipt.print', ['id' => '__ID__']) }}".replace('__ID__', id);

        const win = window.open(
            url,
            '_blank',
            'width=600,height=600,top=100,left=100,scrollbars=yes'
        );

        if (!win) {
            alert('Popup blocked! Please allow popups for this site.');
            return;
        }

        const overlay = document.createElement('div');
        overlay.id = 'printOverlay';
        overlay.style.cssText = `
            position:fixed;
            inset:0;
            background:rgba(0,0,0,0.35);
            z-index:1050;
            cursor:wait;
        `;
        document.body.appendChild(overlay);

        win.onload = () => {
            win.focus();
            win.print();
        };

        const timer = setInterval(() => {
            if (win.closed) {
                clearInterval(timer);
                overlay.remove();
            }
        }, 300);
    }
</script>
@endpush