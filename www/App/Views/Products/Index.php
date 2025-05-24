<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Products Management</h1>
</div>

<!-- Product Form Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <?= isset($this->view->product) ? 'Edit Product' : 'New Product' ?>
        </h6>
    </div>
    <div class="card-body">
        <div id="resultado"></div>
        <form action="/products/save" method="POST" id="form">
            <?php if (isset($this->view->product)): ?>
                <input type="hidden" name="id" value="<?= $this->view->product['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= $this->view->product['name'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" 
                       value="<?= $this->view->product['price'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" 
                       value="<?= $this->view->product['stock'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Variations</label>
                <div id="variations">
                    <?php if (isset($this->view->product['variations'])): ?>
                        <?php foreach ($this->view->product['variations'] as $index => $variation): ?>
                            <div class="variation-item mb-2">
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="variations[<?= $index ?>][name]" 
                                               placeholder="Variation Name" value="<?= $variation['name'] ?>" required>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="number" class="form-control" name="variations[<?= $index ?>][stock]" 
                                               placeholder="Stock" value="<?= $variation['stock'] ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-variation">Remove</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-info btn-sm mt-2" id="addVariation">
                    <i class="fas fa-plus"></i> Add Variation
                </button>
            </div>

            <button type="submit" class="btn btn-primary">
                <?= isset($this->view->product) ? 'Update Product' : 'Save Product' ?>
            </button>
        </form>
        <?php if (isset($this->view->product)): ?>
            <div class="float-right mt-2">
                <button class="btn btn-success btn-sm" onclick="window.location.href='/products'">
                    <i class="fas fa-plus"></i> Add new product
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Products List Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Products List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->view->products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td>R$ <?= number_format($product['price'], 2) ?></td>
                            <td><?= $product['stock'] ?></td>
                            <td>
                                <a href="/products/edit/<?= $product['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-success btn-sm" onclick="addToCart(<?= $product['id'] ?>)">
                                    <i class="fas fa-shopping-cart"></i> Buy
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Shopping Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shopping Cart</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="cartItems"></div>
                <div class="cart-summary mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Subtotal: R$<span id="subtotal">0.00</span></h5>
                            <h5>Shipping: R$<span id="shipping">0.00</span></h5>
                            <h4>Total: R$<span id="total">0.00</span></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Continuar Comprando</button>
                <button type="button" class="btn btn-primary" onclick="checkout()">Finalizar Pedido</button>
            </div>
        </div>
    </div>
</div>

<script>
let variationCount = <?= isset($this->view->product['variations']) ? count($this->view->product['variations']) : 0 ?>;

document.getElementById('addVariation').addEventListener('click', function() {
    const variationsDiv = document.getElementById('variations');
    const newVariation = document.createElement('div');
    newVariation.className = 'variation-item mb-2';
    newVariation.innerHTML = `
        <div class="row">
            <div class="col-md-5">
                <input type="text" class="form-control" name="variations[${variationCount}][name]" 
                       placeholder="Variation Name" required>
            </div>
            <div class="col-md-5">
                <input type="number" class="form-control" name="variations[${variationCount}][stock]" 
                       placeholder="Stock" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-variation">Remove</button>
            </div>
        </div>
    `;
    variationsDiv.appendChild(newVariation);
    variationCount++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-variation')) {
        e.target.closest('.variation-item').remove();
    }
});

function addToCart(productId) {
    fetch('/cart/add/' + productId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateCartModal();
        $('#cartModal').modal('show');
    });
}

function updateCartModal() {
    fetch('/cart/get')
    .then(response => response.json())
    .then(data => {
        const cartItems = document.getElementById('cartItems');
        cartItems.innerHTML = '';
        
        data.items.forEach(item => {
            cartItems.innerHTML += `
                <div class="cart-item mb-2">
                    <div class="row">
                        <div class="col-md-6">${item.name}</div>
                        <div class="col-md-2">$${item.price}</div>
                        <div class="col-md-2">
                            <input type="number" class="form-control form-control-sm" 
                                   value="${item.quantity}" min="1" 
                                   onchange="updateQuantity(${item.id}, this.value)">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        document.getElementById('subtotal').textContent = data.subtotal.toFixed(2);
        document.getElementById('shipping').textContent = data.shipping.toFixed(2);
        document.getElementById('total').textContent = data.total.toFixed(2);
    });
}

function updateQuantity(productId, quantity) {
    fetch('/cart/update/' + productId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(response => response.json())
    .then(data => {
        updateCartModal();
    });
}

function removeFromCart(productId) {
    fetch('/cart/remove/' + productId, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        updateCartModal();
    });
}

function checkout() {
    window.location.href = '/cart';
}
</script>
