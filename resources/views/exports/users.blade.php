<table>
    <thead>
        <tr>
            <th><b>Nome Completo</b></th>
            <th><b>Email</b></th>
            <th><b>Data de Nascimento</b></th>
            <th><b>Data de cadastro</b></th>
            <th><b>Status</b></th>
            <th><b>Cep</b></th>
            <th><b>Rua</b></th>
            <th><b>Bairro</b></th>
            <th><b>Cidade</b></th>
            <th><b>Estado</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->nome }} {{ $user->sobrenome }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->data_nascimento }}</td>
                <td>{{ $user->created_at }}</td>
                <td>{{ $user->status }}</td>
                <td>{{ $user->endereco ? $user->endereco->cep : 'Não informado' }}</td>
                <td>{{ $user->endereco ? $user->endereco->rua : 'Não informado' }}</td>
                <td>{{ $user->endereco ? $user->endereco->bairro : 'Não informado' }}</td>
                <td>{{ $user->endereco ? $user->endereco->cidade : 'Não informado' }}</td>
                <td>{{ $user->endereco ? $user->endereco->estado : 'Não informado' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>