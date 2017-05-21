<!doctype html>
<html>
    <head>
        <title>First page</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/style.css" />
        <link rel="stylesheet" href="../css/responsive.css" />
        <link rel="stylesheet" href="../css/ltr.css" />
        <link rel="stylesheet" href="../css/ltr_responsive.css" />
    </head>
    <body class="">

        <div class="wrapper">
            <header class="main-header">
                <a href="" class="logo">
                    <span class="logo-mini"><b>CwA</b></span>
                    <span class="logo-lg"><b>CMwApp</b></span>
                </a>
                <button id="btn-sidebar-collapse" class="btn btn-primary">=</button>
                <ul class="navig">
                    <li>option</li>
                    <li>option</li>
                    <li>option</li>
                    <li><a href="../">AR</a></li>
                </ul>
            </header>
            <aside class="main-sidebar">
                <section class="sidebar">
                    <ul class="navig">
                        <li>option</li>
                        <li>option</li>
                        <li>option</li>
                        <li>option</li>
                    </ul>
                </section>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <h1>current page</h1>
                </section>
                <section class="content">

                    content

                </section>
            </div>
            <footer class="main-footer">
                <p>CM_App All rights ...</p>
            </footer>
        </div>

        <script src="../js/jquery-1.11.3.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {

                $('#btn-sidebar-collapse').click(function () {

                    if ($('body').hasClass('has-mini-sidebar'))
                        $('body').removeClass('has-mini-sidebar')
                    else
                        $('body').addClass('has-mini-sidebar')
                });

            });
        </script>

    </body>
</html>