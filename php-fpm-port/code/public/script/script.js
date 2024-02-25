const customAlert = document.getElementById('customAlert');

function showAlert() {
    customAlert.style.display = 'block';
}

function hideAlert() {
    customAlert.style.display = 'none';
}

function showEdit(formNumber) {
    let formId = 'form' + formNumber;
    document.getElementById(formId).style.display = 'block';
}

function hideEdit(formNumber) {
    let formId = 'form' + formNumber;
    document.getElementById(formId).style.display = 'none';
}

const searchMaxId = () => {
    let currentId = document.querySelectorAll('li');
    let max = 0;

    currentId.forEach(function (cId) {
        let id = parseInt(cId.id);
        if (id > max) {
            max = id;
        }
    });

    return max;
}

let maxId = searchMaxId();
const errorElement = document.getElementById('error_save');

const actionUser = async (event) => {
    event.preventDefault();
    const name = document.getElementById('name').value;
    const lastname = document.getElementById('lastname').value;
    const birthday = document.getElementById('birthday').value;
    const login = document.getElementById('login').value;
    const password = document.getElementById('password').value;
    const confirm_password = document.getElementById('confirm_password').value;
    const token = document.getElementById('csrf_token').value;

    const formData = new URLSearchParams();
    formData.append('name', name);
    formData.append('lastname', lastname);
    formData.append('birthday', birthday);
    formData.append('login', login);
    formData.append('password', password);
    formData.append('confirm_password', confirm_password);
    formData.append('csrf_token', token);

    const response = await fetch('/user/save', {
        method: 'post',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: formData
    });

    const data = await response.json();

    if ('error' in data) {
        errorElement.innerHTML = data.error;
    } else {
        errorElement.innerHTML = '';
        const separator = document.querySelector('.list__separator');
        const {login, user_name, user_lastname, user_birthday} = data;
        const html = getUsers(maxId + 1, login, user_name, user_lastname, user_birthday);
        separator.insertAdjacentHTML('afterend', html);
    }
};

const alertMessage = document.getElementById('alert_message');

const userDelete = async (id, event) => {
    event.preventDefault();
    const response = await fetch(`/user/delete/?id=${id}`, {
        method: 'get',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    });
    const answer = await response.json();

    const elements = document.querySelectorAll(`[id="${id}"]`);

    elements.forEach(element => {
        element.parentNode.removeChild(element);
    });

    if ('message' in answer) {
        alertMessage.innerHTML = answer.message;
        showAlert();
    }
}

setInterval(() => () => {
    (
        async () => {
            const response = await fetch('/user/indexRefresh', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `maxId=${maxId}`
            });
            const users = await response.json();
            if (users.length !== 0) {
                const separator = document.querySelector('.list__separator');

                const html = users.map(({id, login, user_name, user_lastname, user_birthday}) => {
                    maxId = id;
                    return getUsers(id, login, user_name, user_lastname, user_birthday);
                }).join('');

                separator.insertAdjacentHTML('afterend', html);
            }
        }
    )();
}, 10000)

function getUsers(id, login, user_name, user_lastname, user_birthday) {
    const access = document.getElementsByClassName('list_admin');
    let rowUpdate = '';
    let rowDelete = '';
    if (access.length > 0) {
        rowUpdate = `
                <li id="${id}"><button onclick="showEdit(${id})" class="list__btn" type="submit">&#9998;</button>
                    <div class="alert" id="form${id}">
                      <form class="alert__box" action="/user/update/" method="get">
                        <h5 class="alert__heading">Изменить пользователя</h5>
                        <label for="login">Логин<input id="login" name="login" type="text" value="${login}"></label>
                        <label for="name">Имя<input id="name" name="name" type="text" value="${user_name}"></label>
                        <label for="lastname">Фамилия<input id="lastname" name="lastname" type="text" value="${user_lastname}"></label>
                        <label for="birthday"> Дата рождения<input id="birthday" name="birthday" value="${user_birthday}" type="text"></label>
                        <input type="hidden" name="id" value="${id}">
                        <div class="alert__btn">
                          <button onclick="hideEdit(${id})" type="button">Отмена</button>
                          <button type="submit">Изменить</button>
                        </div>
                      </form>
                    </div>
                </li>
                    `;
        rowDelete = `
                <li id="${id}">
                <form onsubmit="userDelete(${id}, event)" method="get">
                    <button class="list__btn" type="submit">х</button>
                  </form>
                </li>
                    `;
    }

    return `
                <li id="${id}" class="list__left list__flex"><p>${login}</p></li>
                <li id="${id}" class="list__left list__flex"><p>${user_name}</p></li>
                <li id="${id}" class="list__left list__flex"><p>${user_lastname}</p></li>
                <li id="${id}" class="list__flex"><p>${user_birthday}</p></li>
                ${rowUpdate}
                ${rowDelete}
              `;
}