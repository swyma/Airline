function a(){
			var hotel_name=$("#hotel_name").val();
			var hotel_city=$("#hotel_city").val();
			var hotel_level=$("#hotel_level").val();
			//
			var data={hotel_name:hotel_name,hotel_city:hotel_city,hotel_level:hotel_level};
			$.ajax({
				type:"post",
				url:"Hotelinformation/search",
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
	



