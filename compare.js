
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

                jQuery("body").on("click",".remove-compare",function() {
                    /*
                        product_id - идентификатор товара
                        cookie - массив кук
                        position - позиция элемента в массиве
                    */
                    var product_id, cookie, position;
                    // получаем идентификатор товара
                    product_id = jQuery(this).data("product_id");
                    // получаем куку, формируемую плагином
                    cookie = getCookie('yith_woocompare_list');
                    // декодируем из json
                    cookie = JSON.parse(cookie);
                    // вычисляем позицию удаляемого элемента
                    position = cookie.indexOf(product_id);
                    // удаляем элемент из массива
                    cookie.splice(position, 1);
                    // проходим по строкам, удаляя ячейки
                    jQuery(".compare-list tr").each(function() {
                        jQuery("td",this).each(function(){
                            if(jQuery(this).hasClass('product_'+product_id)) {
                                jQuery(this).remove();
                            }
                        });
                    });
                    // удаляем строчку с продуктом из выпадающего окошка
                    jQuery('.compare-item').each( function() {
                        if(jQuery(this).data("product_id")==product_id) {
                            jQuery(this).remove();
                        }
                    });
                    // если элементов не осталось
                    if(cookie.length===0) {
                        // не показываем количество
                        jQuery(".count-compare").html('');
                        // показываем сообщение-заглушку
                        jQuery(".compare-list tbody").empty().html("<tr class='no-products'><td>Нет товаров для сравнения.</td></tr>");
                        // устанавливаем ширину
                        jQuery(".compare-list").css("width","100%");
                    } else {
                        // иначе показываем количество
                        jQuery(".count-compare").html(cookie.length);
                    }
                    // упаковываем массив в json
                    cookie = JSON.stringify(cookie);
                    console.log(cookie);
                    // устанавливаем куку
                    setCookie('yith_woocompare_list', cookie, 1, '/');
                })
            });
