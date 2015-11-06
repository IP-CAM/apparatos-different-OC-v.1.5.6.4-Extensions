<div class="clear"></div>
<div class="box man info">
  <div class="box-heading"><span><?php echo $heading_title; ?></span></div>
  <div class="box-content">
	<?php if ($manufacturers) { ?>
	<ul class="info">
	  <?php foreach ($manufacturers as $manufacturer) {   ?>
	  <li><a  href="<?php echo $manufacturer['href']; ?>">
			<?php if ($manufacturer['image_link']) { ?>				
				<img class="tooltip-4" title="<?php echo $manufacturer['name']; ?>" src="<?php echo $manufacturer['image']; ?>" alt="<?php echo $manufacturer['name']; ?>" />
			<?php } else { ?>
			<span><?php echo $manufacturer['name']; ?></span>
			<?php } ?>
		</a></li>
		
		<?php } ?>
	</ul>
	<?php } ?>
	<div class="clear"></div>
  </div>
</div>