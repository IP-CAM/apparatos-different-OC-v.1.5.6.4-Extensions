<?php 
$class_banner = 'banner';
foreach ($banners as $banner) {
		if($banner['name'] == '21'):
			$class_banner = 'banner1';		
		else:
			$class_banner = '';
		endif;
	 } 	
?>

<div id="banner<?php echo $module; ?>" class="banner <?php echo $class_banner; ?>">
		<?php foreach ($banners as $banner) { ?>
		<?php if ($banner['link']) { ?>
		<div>
			<a href="<?php echo $banner['link']; ?>">
				<img class="<?php echo $banner['title']; ?>" src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" />
				<div class="s-desc <?php echo $banner['title']; ?>"><?php echo $banner['description']; ?></div>
			</a>
		</div>
		<?php } else { ?>
		<div>
			<img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" title="<?php echo $banner['title']; ?>" />
			<div class="s-desc"><?php echo $banner['description']; ?></div>
		</div>
		<?php } ?>
		<?php } ?>
</div>

