<h2><?=$product->name?></h2>
<div class="product-item">
   <img class="product_item__img" src="/images/products/<?=$product->image?>" alt="<?=$product->name?>" >
   <p><?=$product->description?></p>
   <div class="product_item__price"><?=$product->price?> &#8381;</div><a href="#" class="black-button btn-buy" data-id="<?=$product->id?>">В корзину</a>
</div>
<hr>
<?=$feedback?>
