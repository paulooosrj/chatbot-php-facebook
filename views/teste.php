<?php

	if(isset($_POST)){

		echo "   

			var socket = io('https://gentle-ocean-75288.herokuapp.com');
			socket.emit('publish', {
				canal: 'paulo',
				data: {
					'nome': 'Paulao',
					'idade': 15
				}
			});

		";

	}