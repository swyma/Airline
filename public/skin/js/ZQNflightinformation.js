function b(){
			var com_name=$("#com_name").val();
			
			//
			var data={com_name:com_name};
			$.ajax({
				type:"post",
				url:"Flightcompany/flightcomanyshow",
				contentType: "application/x-www-form-urlencoded; charset=utf-8",
				cache: true,
				data:data,
				success:function(data,textStatus){
				//	$("#hello #c-c-iframe").get(0).src="<?php echo $this->baseUrl() . '/Hotelinformation/search'?>";
					$("#hello ").html(data);
					},
				error: function(data, textStatus) { }
			});
	}
	



