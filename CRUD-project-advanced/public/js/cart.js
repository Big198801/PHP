class CartItem extends ItemDynamicList {
    renderTemplate() {
        let total = this.quantity * this.product.price;
        return `
            <div data-total="${total}">
                <a href="/catalog/${this.product_id}">${this.product.name}</a>
            </div>    
            <div class="cart__item_right">    
                <div>${this.quantity} X ${this.product.price} = ${total} &#8381;</div>
                <div class="cart__item_buttons">
                    <a href="" data-cart_id='${this.id}' data-action='subItem' class='cart-item-edit black-button black-button_sm'>-</a>
                    <a href="" data-cart_id='${this.id}' data-action='addItem' class='cart-item-edit black-button black-button_sm'>+</a>
                    <a href="" data-cart_id='${this.id}' data-action='deleteItem' class='cart-item-edit black-button black-button_sm'>X</a>
                </div>
            </div>
        `;
    }

}

class Cart extends DynamicList {

    constructor(idList, pageSize=10, urlApi='/api/cart', itemClassName='cart__item') {
        super(idList, pageSize, urlApi, itemClassName);
        this.total = 0;
        this.changeTotal();
    }

    newItem(id, data) {
        return new CartItem(this.elList, id, data, this.itemClassName);
    }    


    addElEventListeners(id) {
        let btns = document.querySelectorAll('#'+id+' .cart-item-edit')
        btns.forEach((btn) => {
            btn.addEventListener('click', event => {
                this.doFeedBack(
                    btn.dataset['action'],
                    btn.dataset['cart_id']
                );
                event.preventDefault();
            });
        })
    }

    async addItem(id, data, position = 'beforeend') {
        let sum = (!(id in this.items)) ? 0 : Number(this.items[id].product.price * this.items[id].quantity);
        super.addItem(id, data, position);
        this.total += Number(this.items[id].product.price * this.items[id].quantity) - sum;
        this.changeTotal();

    }
    async deleteItem(id) {
        let sum = Number(this.items[id].product.price * this.items[id].quantity);
        super.deleteItem(id);
        this.total -= sum; 
        this.changeTotal();
    }

    changeTotal() {
        document.querySelector('#total_cart').innerHTML = `Итого: ${Number(this.total)} &#8381;`;
        let make_order = document.querySelector('#make_order');  
        make_order.style.display = (Number(this.total) > 0) ? "block" : "none";
    }

    async doFeedBack(action, id) {
        let answer = await application.postJson(this.getURLApi(action), {
            'id': id
        });

        if (!answer || answer.result !== 'ok' || !answer.item) {
            alert("Что-то пошло не так...")
        } else {
            if (answer.item.quantity > 0)  {
                this.addItem(answer.item.id, answer.item, 'afterbegin');
            } else {
                this.deleteItem(answer.item.id);
                this.fillVisible(false);
            }
            application.cartTotalUpdate(answer.count);
        }    
    }

}
