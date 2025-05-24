<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Carrinho de Compras</h1>
</div>

<div id="alertContainer" class="mt-3"></div>
<!-- Content Row -->
<div class="row">
    <?php if (empty($this->view->cart)): ?>
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> Seu carrinho está vazio.
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Cart Items -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Itens do Carrinho</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço</th>
                                    <th>Quantidade</th>
                                    <th>Total</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->view->cart as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['name']) ?></td>
                                        <td>R$ <?= number_format($item['price'], 2, ',', '.') ?></td>
                                        <td>
                                            <div class="input-group" style="width: 120px;">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                                <input type="text" class="form-control text-center" value="<?= $item['quantity'] ?>" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>R$ <?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" onclick="removeItem(<?= $item['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Resumo do Pedido</h6>
                </div>
                <div class="card-body">
                    <!-- Coupon Input -->
                    <div class="form-group">
                        <label>Cupom de Desconto</label>
                        <div class="input-group">
                            <input type="text" id="couponCode" class="form-control" placeholder="Digite o código">
                            <div class="input-group-append">
                                <button class="btn btn-primary" onclick="applyCoupon()">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="form-group">
                        <label>Nome Completo</label>
                        <input type="text" id="customerName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" id="customerEmail" class="form-control" required>
                    </div>

                    <hr>

                    <!-- Order Summary -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>R$ <?= number_format($this->view->subtotal, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frete:</span>
                            <span>R$ <?= number_format($this->view->shipping, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Desconto:</span>
                            <span id="discount">R$ 0,00</span>
                        </div>
                        <div class="d-flex justify-content-between font-weight-bold">
                            <span>Total:</span>
                            <span id="total">R$ <?= number_format($this->view->total, 2, ',', '.') ?></span>
                        </div>
                    </div>

                    <button id="submitOrder" onclick="finishOrder()" class="btn btn-success btn-block">
                        <i class="fas fa-check-circle"></i> Finalizar Pedido
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
let currentDiscount = 0;

function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer');
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            ${message}
        </div>
    `;
}

function updateQuantity(productId, change) {
    fetch(`/cart/update/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            quantity: change
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeItem(productId) {
    if (confirm('Tem certeza que deseja remover este item?')) {
        fetch(`/cart/remove/${productId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function applyCoupon() {
    const code = document.getElementById('couponCode').value;
    if (!code) return;

    fetch('/cart/apply-coupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentDiscount = data.discount;
            updateTotals();
            showAlert('success', 'Cupom aplicado com sucesso!');
        } else {
            showAlert('error', data.error || 'Cupom inválido');
        }
    });
}

function updateTotals() {
    const discountElement = document.getElementById('discount');
    const totalElement = document.getElementById('total');
    
    discountElement.textContent = `R$ ${currentDiscount.toFixed(2).replace('.', ',')}`;
    const newTotal = <?= $this->view->total ?> - currentDiscount;
    totalElement.textContent = `R$ ${newTotal.toFixed(2).replace('.', ',')}`;
}

function finishOrder() {
    const customerName = document.getElementById('customerName').value;
    const customerEmail = document.getElementById('customerEmail').value;
    const couponCode = document.getElementById('couponCode').value;

    if (!customerName || !customerEmail) {
        showAlert('danger', 'Por favor, preencha todos os campos obrigatórios');
        return;
    }

    const submitButton = document.getElementById('submitOrder');
    submitButton.disabled = true;
    submitButton.innerHTML = 'Processando... <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

    fetch('/orders/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            customer_name: customerName,
            customer_email: customerEmail,
            coupon_code: couponCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Pedido realizado com sucesso!');
            setTimeout(() => {
                window.location.href = '/';
            }, 5000);
        } else {
            showAlert('error', data.error || 'Erro ao finalizar pedido');
        }
    }).catch(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Finalizar Pedido';
        showAlert('danger', 'Erro ao conectar com o servidor.');
    });
}
</script> 