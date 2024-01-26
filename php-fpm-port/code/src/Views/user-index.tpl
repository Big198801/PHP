{% include 'user-function.tpl' %}
<p>{{text}}</p>
<ul id="navigation">
    {% for user in users %}
    <li>{{ user.getUserName()}}. День рождения {{ user.getUserBirthday() | date('d.m.Y') }}</li>
    {% endfor %}
</ul>