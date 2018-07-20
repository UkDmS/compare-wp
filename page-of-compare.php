<?php
/*
Template Name: compare
*/
get_header();
get_template_part( 'product_title' );
get_template_part( 'part-breadcrumbs' );
// получаем атрибуты WC
$fields = get_option( 'yith_woocompare_fields');

$default_fields = array(
                'image' => __( 'Image', 'yith-woocommerce-compare' ),
                'title' => __( 'Title', 'yith-woocommerce-compare' ),
                'price' => __( 'Price', 'yith-woocommerce-compare' ),
                'add-to-cart' => __( 'Add to cart', 'yith-woocommerce-compare' ),
                'description' => __( 'Description', 'yith-woocommerce-compare' ),
                'sku'           => __( 'Sku', 'yith-woocommerce-compare' ),
                'stock' => __( 'Availability', 'yith-woocommerce-compare' ),
                'weight'        => __( 'Weight', 'yith-woocommerce-compare' ),
                'dimensions'    => __( 'Dimensions', 'yith-woocommerce-compare' )
            );
foreach ( $fields as $field => $show ) {

    if($show == 1) {
        if ( isset( $default_fields[$field] ) ) {
            $fields[$field] = $default_fields[$field];
        }
        else {
                if ( taxonomy_exists( $field ) ) {
                            $fields[$field] = wc_attribute_label( $field );
                        }

            }
    }
}
unset($fields['add-to-cart']);
unset($fields['sku']);
// print_r($fields);

?>
<style>
a.compare-btn.topbnt.btn.btn-info.fancybox-inline{
width: 320px;
    position: absolute;
    right: 0;
    margin-bottom: 50px;
    display: block;
}</style>

<div class="container">
    <div class="row">
        <main class="col-xs-12" role="main">
        <?
            global $product;
            $products = json_decode($_COOKIE['yith_woocompare_list']);
           // print_r(json_decode($_COOKIE['yith_woocompare_list'])) ;
        ?>
            <div><a class="compare-btn topbnt btn btn-info fancybox-inline" href="#send_compare">
            <i class="fa fa-envelope" aria-hidden="true"></i> Отправить результаты на e-mail</a></div>
            <div style="margin-top: 30px;">
        <? ob_start();?>
            <table class="compare-list" cellpadding="0" cellspacing="0"<?php if ( empty( $products ) ) echo ' style="width:100%"' ?>>
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <?php foreach( $products as $product ) : ?>
                            <td></td>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $products ) ) : ?>
                        <tr class="no-products">
                            <td><?php _e( 'No products added in the compare table.', 'yith-woocommerce-compare' ) ?></td>
                        </tr>
                    <?php else : ?>

                    <tr class="removes">
                        <th>&nbsp;</th>
                        <?php
                        $index = 0;
                        foreach( $products as $product ) :
                            $product_class = ( $index % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $product ?>
                            <td class="<?php echo $product_class; ?>">
<!--                                <a href="?action=yith-woocompare-remove-product&id=<?php echo $product; ?>&redirect=view" data-product_id="<?php echo $product; ?>" class="remove-compare"><?php _e( 'Remove', 'yith-woocommerce-compare' ) ?> <span class="remove">x</span></a>-->
                                <a data-product_id="<?php echo $product; ?>" class="remove-compare"><?php _e( 'Remove', 'yith-woocommerce-compare' ) ?></a>
                            </td>
                            <?php
                            ++$index;
                        endforeach;
                        ?>
                    </tr>
                    <?php
                    foreach ( $fields as $field => $name ) : ?>
                        <tr class="<?php echo $field ?>">
                            <th>
                            <?php
                                if($name=='Изображение') {} else {
                                    echo $name;
                                }
                            ?>
                            <?php if ( $field == 'image' ) echo '<div class="fixed-th"></div>'; ?>
                            </th>

                <?php
                $index = 0;
                foreach( $products as $product ) :
                    $product_class = ( $index % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $product;
                    $item_product = wc_get_product($product);
                ?>
                    <td class="<?php echo $product_class; ?>"><?php
                        switch( $field ) {
                            case 'image':
                                echo '<div class="image-wrap">' . wp_get_attachment_image($item_product->image_id, 'yith-woocompare-image' ) . '</div>';
                                break;
                            case 'title':
                                echo $item_product->get_title();
                                break;
                            case 'price':
                                echo $item_product->get_price_html();
                                break;
                            case 'description':
                                echo get_the_excerpt($product);
                            break;
                            case 'stock':
                                $availability = $item_product->get_availability();
                                if ( empty( $availability['availability'] ) ) {
                                    $availability['availability'] = __( 'In stock', 'yith-woocommerce-compare' );
                                }
                                echo  '<span>'.$availability['availability'].'</span>';

                            break;
                            default:
                                     echo implode( ', ', wc_get_product_terms( $product, $field, array( 'fields' => 'names' ) ) );
                                break;
                        }
                        ?>
                    </td>
                    <?php
                    ++$index;
                endforeach; ?>

            </tr>

        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <script>
            jQuery("document").ready(function(){
                var ind, count=0,ind=0;
                jQuery(".compare-list tbody tr:not([class=image])").each(function(){
                    jQuery("td",this).each(function(){
                        count++;
                        if(jQuery(this).text().replace(/\s{2,}/g, '')==='') {
                          ind++;
                        }
                    });
                    if(ind===count) {
                        jQuery(this).css("display","none");
                    }
                    count = 0;
                    ind = 0;
                });

                function getCookie(name) {
                    var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
                    return matches ? decodeURIComponent(matches[1]) : undefined;
                }

                function setCookie(c_name, value, exdays, path) {
                    exdays = exdays || 30;
                    path = path || '/';

                    var exdate = new Date();
                    exdate.setDate(exdate.getDate() + exdays);
                    var c_value = escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
                    document.cookie = c_name + "=" + c_value + "; path=" + path;
                }

                jQuery("body").on("click",".remove-compare",function(){
                    var product_id, cookie, position;
                    product_id = jQuery(this).data("product_id");
                    cookie = getCookie('yith_woocompare_list');
                    cookie = JSON.parse(cookie);
                    position = cookie.indexOf(product_id);
                    cookie.splice(position, 1);
                    jQuery(".compare-list tr").each(function(){
                    jQuery("td",this).each(function(){
                        if(jQuery(this).hasClass('product_'+product_id)){
                          jQuery(this).remove();
                        }
                    });
                    });
                    jQuery('.compare-item').each( function() {
                        console.log(jQuery(this).data("product_id"));
                        if(jQuery(this).data("product_id")==product_id)
                        {
                            jQuery(this).remove();
                        }
                    });
                    if(cookie.length===0) {
                        jQuery(".count-compare").html('');
                        jQuery(".compare-list tbody").empty().html("<tr class='no-products'><td>Нет товаров для сравнения.</td></tr>");
                        jQuery(".compare-list").css("width","100%");
                    } else
                        jQuery(".count-compare").html(cookie.length);
                    cookie = JSON.stringify(cookie);
                    console.log(cookie);
                    setCookie('yith_woocompare_list', cookie, 1, '/');
                })
            });
            </script>
<style>
table.compare-list .stock td span {
    color: #090;
}
table.compare-list td.odd {
    background: #f7f7f7;
}
table.compare-list td.even {
    background: #fff;
}
 table.compare-list td{
   text-align: center;
}
table.compare-list th{
  text-align: left;
    font-size: 12px;
    font-weight: bold;
    padding-right: 20px;
}
table.compare-list tr{
border-bottom: 1px dashed #3dacc2;
    height: 50px;
}

</style>
            <?
                $output = ob_get_contents();
                ob_end_clean();
                $path = "comparehtml/".time().'.html';
                $fp = fopen($path, 'w');
                fwrite($fp, $output);
                fclose($fp);
                echo $output;
            ?>
            </div>
        </main>
    </div>
</div>
<div style="display:none" class="fancybox-hidden">
<div id="send_compare">
		<form action="" method="post" class="" novalidate="novalidate">
			<div class="form-he4der">Сравнение товаров</div>
			<p class="smallcomment" style="text-align: center">
                Пожалуйста, введите Ваше имя и адрес электронной <br>почты, на который хотите получить результат сравнения товаров:
			</p>
            <p>Ваше имя <br>
			    <span class="wpcf7-form-control-wrap your-name">
			    	<input type="text" name="your-name" required="" value="" size="40" class="wpcf7-form-control wpcf7-text username" aria-invalid="false" placeholder="Как к Вам обращаться?">
			    </span>
			</p>
			<p>E-mail*<br>
			    <span class="wpcf7-form-control-wrap your-email">
			    	<input type="text" name="your-email" value="" size="40" required="" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required email" aria-required="true" aria-invalid="false" placeholder="Для обратной связи">
			    </span>
			    </p>
			<p style="text-align: center;">Нажимая на кнопку «Отправить», <br>Вы принимаете условия <a href="https://www.beautysystems.ru/privacy-policy/" target="_blank">Пользовательского соглашения</a>.</p>
                <input type="hidden" name="attachment" value="<?=$path; ?>" />
			<p class="form-button">
				<input type="checkbox" required="" name="agree-safe" value="1" style="display: none;" class="agree-safe">
				<input type="submit" value="Отправить" class="btn btn-primary">
				<span class="ajax-loader"></span>
			</p>
		</form>
	</div>
	 		</div>



<?php get_footer(); ?>