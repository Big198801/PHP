class App {
    async getJson(url){
        try {
            const result = await fetch(url);
            return await result.json();
        } catch (error) {
            console.log(error);
        }
    }

    async postJson(url, data) {
        try {
            const result = await fetch(url, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            });
            return await result.json();
        } catch (error) {
            console.log(error);
        }
    }

    async putJson(url, data) {
        try {
            const result = await fetch(url, {
                method: 'PUT',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            });
            return await result.json();
        } catch (error) {
            console.log(error);
        }
    }

    async deleteJson(url, data) {
        try {
            const result = await fetch(url, {
                method: 'DELETE',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            });
            return await result.json();
        } catch (error) {
            console.log(error);
        }
    }    

    cartTotalUpdate(count) {
        document.getElementById("header_cart").innerText = "Корзина (" + Number(count) + ")"; 
    }
    
    addListenerToBtnBuy(querySelector) {
        let buy_btn = document.querySelector(querySelector);
        buy_btn.addEventListener('click', async event => {
            let answer = await application.postJson('/api/cart/addItem', {
                'product_id': buy_btn.dataset['id']
            });

            if (!answer || answer.result !== 'ok') {
                alert("Что-то пошло не так...")
            } else {
                this.cartTotalUpdate(answer.count);
            };    
            event.preventDefault();
        });
    }

}

var application = new App();

