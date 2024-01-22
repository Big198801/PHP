<p>Добавить пользователей в хранилище:</p>
<form action="/user/save/" method="get">
        <label for="name">Имя
                <input id="name" name="name" type="text" placeholder="Иван Иванов" required autofocus>
        </label>
        <label for="birthday"> Дата рождения
                <input id="birthday" name="birthday" placeholder="31-12-1970" type="text" required>
        </label>
        <button type="submit">Отправить</button>
</form><br>

<p>Список пользователей в хранилище:</p>
<ul id="navigation">
    {% for user in users %}
        <li>{{ user.getUserName()}}. День рождения {{ user.getUserBirthday() | date('d.m.Y') }}</li>
    {% endfor %}
</ul>