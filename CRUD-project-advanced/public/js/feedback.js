class FeedbackItems extends ItemDynamicList {
    renderTemplate() {
        return `
        <div data-name='${this.name}' data-message='${this.feedback}' data-id='${this.id}' id='${"fb_"+this.id}'>
           <p class="feedback-author">${this.name}:</p> 
           <div>
               ${this.feedback} <a href="" data-id='${this.id}' class='edit black-button black-button_sm'>Править</a> 
               <a href="" data-id='${this.id}' class='delete black-button  black-button_sm'>Х</a>
            </div>
            <hr>
        </div>`;
    }

}

class Feedback extends DynamicList {
    constructor(idList, pageSize = 20, urlApi = '/api/feedback', itemClassName='feedback-item', idForm='', categoryFeedback='', groupId='') {
        super(idList, pageSize, urlApi, itemClassName);
        this.categoryFeedback = categoryFeedback;
        this.groupId = groupId;
        this.form = (idForm) ? document.getElementById(idForm) : null;
    }


    addEditEventListener($func) {
        this.editEventListener = $func;
    }

    getJsonBody(count, offset) {
        return {'count': count, 'offset': offset, 'categoryFeedback': this.categoryFeedback, 'groupId' : this.groupId };
    }

    newItem(id, data) {
        return new FeedbackItems(this.elList, id, data, this.itemClassName);
    }    

    addElEventListeners(id) {
        let delete_btn = document.querySelector('#'+id+' .delete');
        delete_btn.addEventListener('click', event => {
            this.doFeedBack(
                'delete',
                delete_btn.dataset['id']
            );
            event.preventDefault();
        });

        let edit_btn = document.querySelector('#'+id+' .edit');
        edit_btn.addEventListener('click', event => {
            if (this.editEventListener) {
                this.editEventListener(id);
            }
            event.preventDefault();
        });
    }

    async doFeedBack(action, id, name='', feedback='') {
        let answer = await application.postJson(this.getURLApi(action), {
            'id': id,
            'name': name, 
            'feedback': feedback,
            'categoryFeedback': this.categoryFeedback,
            'groupId': this.groupId
        });

        if (!answer || answer.result !== 'ok') {
            alert("Что-то пошло не так...")
        } else if (action == 'add' || action == 'edit')  {
            this.addItem(answer.id, answer, 'afterbegin');
        } else if (action == 'delete')  {
            this.deleteItem(answer.id);
            this.fillVisible(false);
        };    
    }

}
