class NewsItems extends ItemDynamicList {
    renderTemplate() {
        return `
        <a class="news-item__link" href="/news/${this.id}">
             <p>${this.title}</p>
        </a>
        `;
    }

}

class News extends DynamicList {
    constructor(idList, pageSize = 20, urlApi = '/api/news', itemClassName='news-item') {
        super(idList, pageSize, urlApi, itemClassName);
    }

    newItem(id, data) {
        return new NewsItems(this.elList, id, data, this.itemClassName);
    }    

}
