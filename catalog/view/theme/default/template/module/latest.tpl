<div class="box">
  <div class="box-heading"><?php echo $heading_title; ?>
  <span class="right"><a href="<?php echo $view_all ?>">View all</a></span>
  </div>
  <div class="box-content" id="latest_carousel">
    <div class="xbox-product">
    <ul class="jcarousel-skin-opencart" id="latest">
      <?php foreach ($products as $product) { ?>
      <li>
      <div>
        <?php if ($product['thumb']) { ?>
        <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a></div>
        <?php } ?>
        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
        <?php if ($product['price']) { ?>
        <div class="price">
          <?php if (!$product['special']) { ?>
          <?php echo $product['price']; ?>
          <?php } else { ?>
          <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
          <?php } ?>
        </div>
        <?php } ?>
        <?php if ($product['rating']) { ?>
        <div class="rating"><img src="catalog/view/theme/default/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></div>
        <?php } ?>
        <div class="cart"><a onclick="addToCart('<?php echo $product['product_id']; ?>');" class="button"><span><?php echo $button_cart; ?></span></a></div>
      </div>
      </li>
      <?php } ?>
      </ul>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('#latest').jcarousel({
	vertical: false,
	visible: 5,
	scroll: 3
});
//--></script>
