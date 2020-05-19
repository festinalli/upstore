
<style type="text/css" media="screen">
	*{
		font-family: 'Arial';
	}
	.container{
		height: 100vh;
	}
	.header{
		width:100%;
		height:100px;
		background-color:#FFF;
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.dados-mensage{
		width:600px;
		background-color:#FFF;
		margin:0 auto;
		padding:25px;
		list-style: none;
		color:#696969;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.title{
		font-size: .8em;
		color:#696969;
		text-align: center;
	}

	footer{
		background-color:#000;
		height:auto;
		color:#FFF;
		box-shadow: 0px 0px 7px rgba(0,0,0,.5);
		font-family: 'Georgia';
		text-align: center;
		padding:20px 0;
		font-size:.7em;
	}

	.puzzle a{
		color:#DCDCDC;
		text-decoration: none;
	}

	.puzzle img{
		margin: 5px;
	}

	.button{
		padding:10px 25px;
		background-color:#000;
		color:#FFF;
		text-align: center;
		text-transform: uppercase;
		text-decoration: none;
		margin:10px auto;
		border-radius: 5px;
		box-shadow: 0px 0px 7px rgba(0,0,0,.5);
		display: block;
	}	

	.blue{
		color:blue;
		font-size: .6em;
		display: block;
		margin:10px 0;
	}

	.width-100{
		text-align: center;
		padding:20px 0;
		color:#696969;
	}

</style>
<div class="container">
	<div class="header">
		<img src="{{url('img/logo_upstore.png')}}" alt="Upstore">
	</div>
	<h5 class="title">
		Email de recuperação de senha!<br>
		<small>Se você não solicitou a recuperação de senha da sua conta ignore este email</small>
	</h5>

	<div class="dados-mensage">
				<a href="{{$user->link}}" title="Recuperar Senha" class="button">Recuperar senha</a>
	</div>
		<div class="width-100">
			<p>Ou copie e cole este link no seu navegador:</p>
						<span class="blue">{{$user->link}}</span> 
		</div>
	<footer>
			<p>&copy; UPSTORE <br> {{date('Y')}} </p>
			<span class="puzzle"><a href="https://puzzlelab.com.br/lancadora-startup" title="Puzzle">Desenvolvido por: Puzzle.lab <br>
				<img src="{{url('img/puzzle.jpg')}}" alt="" width="25px"></a></span>
	</footer>
</div>

