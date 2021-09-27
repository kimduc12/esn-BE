<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" ></script>
        <style>
            body {
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <h1>Form test API</h1>
                    <div class="btn btn-primary">Submit</div>
                </div>
            </div>
        </div>
    </body>
    <script>
        $(document).ready(function(){
            $('.btn').on('click', function(){
                var headers = {
                    "Authorization": "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiYjMyN2ZkZjM1MDIzNjU1NjNkOWZlNTQyMjFlOGI4MmFkYTQyZTc4NTZlNTdmYzUyODQ5OTlkNjc3NmRhNWMwNWFiOThlMTA0OTNhY2JiYTYiLCJpYXQiOiIxNjExNjYyNjU1Ljc5NzkyMiIsIm5iZiI6IjE2MTE2NjI2NTUuNzk3OTI3IiwiZXhwIjoiMTY0MzE5ODY1NS43OTU2MDgiLCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.HcSaxHjLE6pE20hCNcveik9V1dPLNyzf5Oo90sVQo-MxN2ofv9WafuyQohaFHqS1XLJkpp9IWPrLFz_2s3JfwF1uxJ09TruUBoTbD0HvFB4Py3WGK3-Xk_UrAbzc2LdUCURssXsw8TZwfG1wpxsMlGxF6VnFZMTmP1oHE0rP8qizeszL-5w7Ix3QRbC2FYm3v7Aq2P9ekuGkl3LFiHiGXOkQeFgFtbauI4iYqWkqzBZaTpNDOhlKjUF5enwUDPLqKEn5H5RkxXEp9aLf1gHWmA8WGX4JxnaNuCasBT1KYbKPwIdUv9P6HqbhuSbEuNT9Zq-PDDJO1uPIMQ8RVM-eSPTcEgYCwsDhMI6PhKCGrd2H6NzKfPFiHb32fp8NKyCdwLFXVIf7C6cEoC8DgJbVisbBFUazuqcrTymQEUdKYINAtcYbxPPoGI6RycmB2t2FCneyIMQUQqAoRgR4T_6hQF7u5p9otSj3HC-i3yvjei6L8QfGpi2GvJxyhKnbEGkfMhJ2fLGzP3XOlOyCGaZXEybaXaSSC-6L4Qnz-FFVg3hmKyrqVOgr1S-a7OS-sn0mdlLGArexBZsdTYO25PGNJ7tfXknCfOND4Z2V7s9FZtwqJ49Xn9XkbRAMSNWKNX1OyjaLqdjAXlZY13hcVsOTccV3ZdMvDiC-e4Mqglsqpog",
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                };
                $.ajax({
                    method: "POST",
                    url: "http://apitest.childrensalon.vn/api/users",
                    data: {
                        zipcode: 97201
                    },
                    headers: headers,
                    success: function( res ) {
                        console.log(res)
                    }
                });
                $.ajax({
                    method: "POST",
                    url: "http://apitest.childrensalon.vn/api/login",
                    data: {
                        zipcode: 97201
                    },
                    headers: headers,
                    success: function( res ) {
                        console.log(res)
                    }
                });
                $.ajax({
                    method: "GET",
                    url: "http://apitest.childrensalon.vn/api/users/get-my-info",
                    data: {
                        zipcode: 97201
                    },
                    headers: headers,
                    success: function( res ) {
                        console.log(res)
                    }
                });
            });
        });
    </script>
</html>
