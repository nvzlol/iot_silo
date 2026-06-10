<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<style>
body{
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    background:#1e4ea1;
    font-family:sans-serif;
}
.box{
    background:white;
    padding:30px;
    border-radius:10px;
    width:300px;
    text-align:center;
}
input{
    width: 85%;          
    padding:10px;
    margin:10px auto;    
    display:block;
    border-radius:5px;
    border:1px solid #ccc;
}
button{
    width: 85%;
    padding:10px;
    margin:10px auto;
    display:block;
    background:#1e4ea1;
    color:white;
    border:none;
    border-radius:5px;
    cursor:pointer;
}
.error{
    color:red;
    font-size:14px;
}
</style>
</head>

<body>
<div class="box">
    <h2>SiloTrack Login</h2>

    <!-- FORM LOGIN -->
    <form method="POST" action="/login">
        @csrf

        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <!-- ERROR MESSAGE -->
    @if(session('error'))
        <p class="error">{{ session('error') }}</p>
    @endif

</div>
</body>
</html>