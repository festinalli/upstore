
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
		min-height:300px;
		background-color:#FFF;
		margin:0 auto;
		padding:25px;
		list-style: none;
		color:#696969;
	}

	.dados-mensage li{
		margin:10px 0;
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
</style>
<div class="container">
	<div class="header">
		<img src="{{url('img/logo_upstore.png')}}" alt="Upstore">
	</div>
	<h5 class="title">Mensagem enviada através do formulário de contato !</h5>
	<ul class="dados-mensage">
		<li><b>Nome:</b> <span>{{$contato->nome}}</span></li>
		<li><b>E-mail:</b> <span>{{$contato->email}}</span></li>
		<li>
			<b>Mensagem:</b>
			<p>
				{{$contato->mensagem}}
			</p>
		</li>
	</ul>
	<footer>
			<p>&copy; UPSTORE <br> {{date('Y')}} </p>
			<span class="puzzle"><a href="https://puzzlelab.com.br/lancadora-startup" title="Puzzle">Desenvolvido por: Puzzle.lab <br>
				<img src="{{url('img/puzzle.jpg')}}" alt="" width="25px"></a></span>
	</footer>
</div>

