<?php echo $header; ?>
<?php if ($warning) { ?>
    <div class="warning"><?php echo $warning; ?><img src="catalog/view/theme/default/image/close.png" alt="" class="close" /></div>
<?php } ?>
<?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <h1><?php echo $heading_title; ?></h1>

    <p><?php echo $text_logged; ?></p>
    <p><?php echo $text_information; ?></p>
    <form id="preorder" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <div class="checkout-product">
            <table>
                <thead>
                    <tr>
                        <td class="name"><?php echo $column_name; ?></td>
                        <td class="model"><?php echo $column_model; ?></td>
                        <td class="quantity"><?php echo $column_quantity; ?></td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product) { ?>
                        <tr>
                            <td class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                                <?php foreach ($product['option'] as $option) { ?>
                                    <br />
                                    &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                                <?php } ?></td>
                            <td class="model"><?php echo $product['model']; ?></td>
                            <td class="quantity"><?php echo $product['quantity']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php if (!$text_logged) { ?>
            <div class="box">
                <h2><?php echo $text_your_details; ?></h2>
                <div class="content">
                    <table class="form">
                        <tr>
                            <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
                            <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
                                <?php if ($error_firstname) { ?>
                                    <span class="error"><?php echo $error_firstname; ?></span>
                                <?php } ?></td>
                        </tr>
                        <tr>
                            <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
                            <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
                                <?php if ($error_lastname) { ?>
                                    <span class="error"><?php echo $error_lastname; ?></span>
                                <?php } ?></td>
                        </tr>
                        <tr>
                            <td><span class="required">*</span> <?php echo $entry_email; ?></td>
                            <td><input type="text" name="email" value="<?php echo $email; ?>" />
                                <?php if ($error_email) { ?>
                                    <span class="error"><?php echo $error_email; ?></span>
                                <?php } ?></td>
                        </tr>
                        <tr>
                            <td><span class="required">*</span> <?php echo $entry_telephone; ?></td>
                            <td><input type="text" name="telephone" value="<?php echo $telephone; ?>" />
                                <?php if ($error_telephone) { ?>
                                    <span class="error"><?php echo $error_telephone; ?></span>
                                <?php } ?></td>
                        </tr>
                    </table>
                </div>

            </div>
        <?php } ?>
    </form>
    <div class="buttons">
        <div class="right"><a onclick="$('#preorder').submit()" class="button"><span><?php echo $button_confirm; ?></span></a></div>
    </div>
    <?php echo $content_bottom; ?>
</div>
<?php echo $footer; ?>