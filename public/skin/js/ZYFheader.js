/**点击导航条时，导航条自动加载hover的css
 * 编辑人：周永丰
 */
function addHover()
{
    var domainUrl = ['index','hotelinformation','flightcompany','bookinformation','customer','customerservice','sitehelp','aboutus'];
    var url=window.location.href;
    $(".navi li a").each(function (i) {
        if (url.toLocaleLowerCase().indexOf(domainUrl[i])>0) {
            //onhover是个a被选中的样式，具体你可以自己写
        	$(".navi li a").removeClass("hover");
            $(this).addClass("hover");
            return;
        }
    });	
}
/**
 * 调用addHover()
 * 编辑人：周永丰
 */
addHover();