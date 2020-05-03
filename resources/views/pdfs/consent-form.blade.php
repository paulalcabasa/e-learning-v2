<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Consent Privacy Form</title>

    <style>
        /** 
            Set the margins of the page to 0, so the footer and the header
            can be of the full height and width !
            **/
        @page { margin: 0cm 0cm; }

        /** Define now the real margins of every page in the PDF **/
        body {
            font-family:'Calibri','Sans-serif','Arial';
            margin-top: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
            margin-bottom: 0cm;
        }

        header {
            position: fixed;
            top: 0px;
            left: 0px;
            right: 0px;
            height: 50px;
            padding:2.5em 1em 1em 1em;

            font-size:12px;
            margin-left: 1cm;
            margin-right: 1cm;

        }

        main {
            margin-left: 0.3cm;
            margin-right: 0.3cm;
        }

        .confidential {
            width:11%;
            background-color:#000;
            color:#fff;
            margin:0;
            font-size:11px;
            padding:.5em 1em;
            font-weight:bold;
        }

        .item-table {
            font-size:11px;
            width:100%;
        }

        .item-table thead tr th { text-align: center; }

        .item-table tfoot { text-align: center; }

        .item-data-style1 { border-bottom:1px solid #000; }

        .item-data-style2 {
            border-bottom:1px solid #000;
            text-align: center;
        }

        .terms {
            font-size:11px;
            margin-top:1em;
        }

        .text-bold { font-weight: bold; }

        .tab { text-indent: 40px }

        table {
            font-size: x-small;
        }
        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        /* New CSS */
        .pdf-container {
            padding: 0 35px 0 35px;
        }
        .logo {
            margin-top: 10px;
            margin-bottom: 2px;
            height: 18px;
        }
        .section-header {
            
        }
        .section-header 
        .section-header-row {
            background-color: #3F3F3F;
        }
        .section-table {
            border-collapse: collapse;
            width: 100%;
        }
        .section-table-child {
            border-collapse: collapse;
            width: 100%;
            border-top: none;
        }
        .section-th {
            padding: 10px;
            color: white;
            font-weight: normal;
        }
        .section-td {
            padding: 10px;
        }
        .section-table, 
        .section-table-child,
        .section-thead, 
        .section-td {
            border: .5px solid #3F3F3F;
        }
    </style>
</head>
<body>
    <header>
        <table width="100%" style="margin-bottom:-0.5em;">
            <tr>
                <td align="left">
                    <img src="{{ asset('public/images/isuzu-logo-compressor.png') }}" height="25" /><br/>
                    <span style="font-size:11px;">Isuzu Philippines Corporation</span>
                </td>
                <td align="right" style="vertical-align: bottom;font-size:11px;">E-Learning System</td>
            </tr>
        </table>
        <hr />
        <div class="confidential">CONFIDENTIAL</div>
    </header>
    <br><br>
    <main>
        <div>
            <h4 style="text-align:center;">
                Consent Disclosure Statement for <br>
                Isuzu E-Learning System
            </h4>
        </div>

        <div>
            <blockquote>
                <p class="tab">I hereby give my consent to Isuzu Philippines Corporation (IPC), to the collection, 
                    transmission, distribution, retention, and destruction of my personal information in full compliance 
                    with the Data Privacy Act of 2012 of the Republic of the Philippines.</p>

                
            </blockquote>
        </div>

        <div style="margin: 0 40px">
            <table 
                border="0" 
                style="border-collapse: collapse; width:100%; font-size:11px; padding: 10px; margin-top: 150px;"
            >
                <tr>
                    <td>
                        <hr align="right" width="25%">
                    </td>
                </tr> 
                <tr>
                    <td style="font-weight:bold; text-align: right;">
                        Signature Over Printed Name
                    </td>
                </tr> 
            </table>
        </div>

        <div class="section-header" style="margin: 0 40px">
            <table class="section-table">
                <tr class="section-header-row">
                    <th 
                        colspan="2"
                        class="section-th" 
                        style="text-align: center; font-weight: bold"
                    >
                        Trainee Details
                    </th>
                </tr>
                <tr>
                    <td class="section-td" width="10">
                        Firstname
                    </td>
                    <td class="section-td" width="90">
                        
                    </td>
                </tr>
                <tr>
                    <td class="section-td" width="10">
                        Middlename
                    </td>
                    <td class="section-td" width="90">
                        
                    </td>
                </tr>
                <tr>
                    <td class="section-td" width="10">
                        Lastname
                    </td>
                    <td class="section-td" width="90">
                        
                    </td>
                </tr>
                <tr>
                    <td class="section-td" width="10">
                        Email
                    </td>
                    <td class="section-td" width="90">
                        
                    </td>
                </tr>
            </table>
        </div>

        

    </main>
</body>
</html>