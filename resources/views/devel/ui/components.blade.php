<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>UI components</title>
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    
    <div class="m-3 font-sans">
        <h1>Heading H1</h1>
        <h2>Heading H2</h2>
        <h3>Heading H3</h3>
        <h4>Heading H4</h4>
        <h5>Heading H5</h5>


        <hr>
        <h2>Form components</h2>

        <input type="checkbox" id="toggle" class="offscreen" checked/>
        <label for="toggle" class="switch"></label>
        
        <input type="checkbox" id="toggle-nm" class="offscreen"/>
        <label for="toggle-nm" class="switch"></label>

        <input type="checkbox" id="toggle-sm" class="offscreen"/>
        <label for="toggle-sm" class="switch-sm"></label>




        <form action="">
            <input type="text">
            <input type="checkbox" name="pok" id="ahoj">
        </form>

        <hr>

        <button class="text-xs font-semibold rounded-full px-4 py-1 leading-normal bg-white border border-purple text-purple hover:bg-purple hover:text-white">Message</button>
        <button class="btn btn-blue">btn btn-blue</button>
        <button class="btn-fr btn-blue">btn-fr btn-blue</button>
        <button class="btn btn-blue-outline">btn btn-blue-outline</button>
        <button class="btn-fr btn-blue-outline">btn-fr btn-blue-outline</button>

    </div>
    

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>