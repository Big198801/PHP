<section class="operation">
    <form action="/user/save/" method="get">
        <fieldset>
            <legend>Добавить пользователя</legend>
            <label for="name">Имя
                <input id="name" name="name" type="text" placeholder="Иван Иванов" required autofocus>
            </label>
            <label for="birthday"> Дата рождения
                <input id="birthday" name="birthday" placeholder="31-12-1970" type="text" required>
            </label>
        </fieldset>
        <button type="submit">Добавить</button>
    </form>
    <div>
        <form action="/user/delete/" method="get">
            <fieldset>
                <legend>Удалить пользователя</legend>
                <label for="name">Имя
                    <input id="name" name="name" type="text" placeholder="Иван Иванов" required autofocus>
                </label>
            </fieldset>
            <button type="submit">Удалить</button>
        </form>
        <form action="/user/clear/" method="get">
            <button type="submit">Очистить</button>
        </form>
    </div>
    <form action="/user/search/" method="get">
        <fieldset>
            <legend>День рождения</legend>
            <p>Сегодня {{ "now"|date("d-m-Y") }} и +10 дней</p>
        </fieldset>
        <button type="submit">Показать</button>
    </form>
</section>