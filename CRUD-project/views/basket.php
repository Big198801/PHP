<h2>Корзина</h2>

<?php foreach ($basket as $item):?>
    <div id="<?= $item['basket_id'] ?>">
        <h3><?=$item['name']?></h3>
        <p>price: <?=$item['price']?></p>
        <button data-id="<?= $item['basket_id'] ?>" class="delete">Удалить</button>
    </div>
<?php endforeach;?>
<script>
    let buttons = document.querySelectorAll('.delete');
    buttons.forEach((elem) => {
        elem.addEventListener('click', () => {
            let id = elem.getAttribute('data-id');
            (
                async () => {
                    const response = await fetch('/basket/delete/?id=' + id);
                    const answer = await response.json();
                    document.getElementById('count').innerText = answer.count;
                    document.getElementById(id).remove();
                }
            )();
        })
    })
</script>

