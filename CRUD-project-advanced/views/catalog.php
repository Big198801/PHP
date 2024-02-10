<h2>Каталог</h2>
<div id="catalog" class="products">
</div>
<script src="/js/catalog.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", event => {
        catalog = new Products('catalog', 10);
        catalog.fillVisible();
    });
</script>