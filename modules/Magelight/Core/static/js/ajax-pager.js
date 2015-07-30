/**
 * Created with JetBrains PhpStorm.
 * User: User
 * Date: 18.01.13
 * Time: 17:31
 * To change this template use File | Settings | File Templates.
 */

function bindPagers()
{
    $('div.pagination.ajax-pager').each(function(){
        var pager = $(this);
        pager.find('a').unbind('click').bind('click', function (e) {
            e.preventDefault();
            var link = $(this);
            var href = link.attr('href');
            if (href[0] == '#') {
                return;
            }
            var target = pager.attr('data-target-selector');
            $.ajax({
                url: href,
                type: 'GET'
            }).done(function (html) {
                    $(target).html(html);
                    bindPagers();
                });
        });
    });
}

$(document).ready(function () {
    bindPagers();
});