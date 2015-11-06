<div class="clear"></div>
</div>
</div>
</div>
<div class="clear"></div>
</section>
<!-- Footer -->
<footer>
	<div class="container">
		<div class="row">
			<div class="col-sm-3">
				<div class="block">
					<div class="block-heading">
						<?php echo $text_extra; ?>
					</div>
					<div class="block-content">
						<ul>
							<li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
							<li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
							<li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
							<li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
						</ul>
					</div>
				</div>
			</div>
			<?php if ($informations) { ?>
			<div class="col-sm-3">
				<div class="block">
					<div class="block-heading">
						<?php echo $text_information; ?>
					</div>
					<div class="block-content">
						<ul>
							<?php foreach ($informations as $information) { ?>
							<li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="col-sm-3">
				<div class="block">
					<div class="block-heading">
						<?php echo $text_account; ?>
					</div>
					<div class="block-content">
						<ul>
							<li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
							<li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
							<li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
							<li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="block">
					<div class="block-heading">
						<?php echo $text_service; ?>
					</div>
					<div class="block-content">
						<ul>
							<li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
							<li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
							<li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
						</ul>
					</div>
				</div>
			</div>			
		</div>
	</div>
	
	<div class="block-contact">
		<div class="container">
			<div class="row">				
				<div class="col-sm-4">
					<div class="foot-address">
						<div><?php echo $address; ?></div>
					</div>	
				</div>
				<div class="col-sm-4">					
					<div class="foot-mail">
						<div><span class="icon">@</span><a href="mailto:<?php echo $email; ?>" ><?php echo $email; ?></a></div>
					</div>	
				</div>
				<div class="col-sm-4">
					<ul class="social">
						<li><a title="<?php echo $text_fb; ?>" class="tooltip-2" href="http://facebook.com"><i class="fa fa-facebook"></i></a></li>
						<li><a title="<?php echo $text_gglp; ?>" class="tooltip-2" href="http://accounts.google.com"><i class="fa fa-google-plus"></i></a></li>
						<li><a title="<?php echo $text_rss; ?>" class="tooltip-2" href="#"><i class="fa fa-rss"></i></a></li>
						<li><a title="<?php echo $text_pn; ?>" class="tooltip-2" href="http://pinterest.com"><i class="fa fa-pinterest"></i></a></li>						
						<li><a title="<?php echo $text_in; ?>" class="tooltip-2" href="http://linkedin.com"><i class="fa fa-linkedin"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	
	<div id="copyright">
		<!-- copyright -->
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<?php echo $powered; ?><!-- [[%FOOTER_LINK]] -->
				</div>
			</div>
		</div>
	</div>
</footer>
<script type="text/javascript" 	src="catalog/view/theme/<?php echo $this->config->get('config_template');?>/js/livesearch.js"></script>
</div>
</div>
</div>
</body></html>