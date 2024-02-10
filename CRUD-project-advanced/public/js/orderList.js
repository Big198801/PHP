
class OrderItems extends ItemDynamicList {
    renderTemplate() {
        return `
        <tr>
            <td><a href="/order/${this.uId}">${this.date}</a></td>
            <td>${this.id}</td>
            <td>${this.total}</td>
            <td>${this.status}</td>
        </tr>
        `;
    }

}

class Orders extends DynamicList {
    constructor(idList, pageSize = 10, urlApi = '/api/orderList', itemClassName='order-item') {
        super(idList, pageSize, urlApi, itemClassName);
    }

    newItem(id, data) {
        return new OrderItems(this.elList, id, data, this.itemClassName);
    }    

}