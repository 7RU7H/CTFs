<?php
//
// World Time Server Dashlet
// Copyright (c) 2008-2018 Nagios Enterprises, LLC. All rights reserved.
//

include_once(dirname(__FILE__) . '/../dashlethelper.inc.php');


// Run the initialization function
worldtimeserver_dashlet_init();


function worldtimeserver_dashlet_init()
{

    $name = "worldtimeserver";

    $args = array(
        DASHLET_NAME => $name,

        DASHLET_VERSION => "2.0.0",
        DASHLET_AUTHOR => "Nagios Enterprises, LLC",
        DASHLET_DESCRIPTION => _("World Time Clocks for locations around the world, powered by") . " <a href='http://www.worldtimeserver.com/' target='_blank'>www.worldtimeserver.com</a>.",
        DASHLET_COPYRIGHT => "Dashlet Copyright &copy; 2009-2018 Nagios Enterprises.<br>Data Copyright &copy; Chaos Software Group, Inc.",
        DASHLET_LICENSE => "MIT",
        DASHLET_HOMEPAGE => "https://www.nagios.com",

        DASHLET_FUNCTION => "worldtimeserver_dashlet_func",

        DASHLET_TITLE => _("World Time Clock"),

        DASHLET_OUTBOARD_CLASS => "worldtimeserver_outboardclass",
        DASHLET_INBOARD_CLASS => "worldtimeserver_inboardclass",
        DASHLET_PREVIEW_CLASS => "worldtimeserver_previewclass",

        DASHLET_WIDTH => "300",
        DASHLET_HEIGHT => "200",
        DASHLET_OPACITY => "1.0",
        DASHLET_BACKGROUND => "",
    );

    register_dashlet($name, $args);
}


/**
 * @param string $mode
 * @param string $id
 * @param null   $args
 *
 * @return string
 */
function worldtimeserver_dashlet_func($mode = DASHLET_MODE_PREVIEW, $id = "", $args = null)
{

    $output = "";

    $imgbase = get_dashlet_url_base("worldtimeserver") . "/images/";

    switch ($mode) {

        case DASHLET_MODE_GETCONFIGHTML:
            $output = '

            <div class="form-group">
                <label for="clocklocation">'._('Location').'</label>
                <div>
                    <select class="form-control" name="clocklocation" id="clocklocation">
                    ' . worldtimeserver_get_location_options() . '
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="clocktype">'._('Clock Type').'</label>
                <div>
                    <select name="clocktype" class="form-control" id="clocktype">
                        <option value="12">'._('12 Hour').'</option>
                        <option value="24">'._('24 Hour').'</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="clockname">'._('Clock Name').'</label>
                <div>
                    <input type="text" name="clockname" class="form-control" id="clockname">
                </div>
            </div>

            <div class="form-group">
                <label for="clocksize">'._('Clock Size').'</label>
                <div>
                    <select name="clocksize" class="form-control" id="clocksize">
                        <option value="small">'._('Small').'</option>
                        <option value="large">'._('Large').'</option>
                    </select>
                </div>
            </div>
            ';
            break;

        case DASHLET_MODE_OUTBOARD:
        case DASHLET_MODE_INBOARD:
            $id = uniqid();
            $clocklocation = $args["clocklocation"];
            $clocktype = $args["clocktype"];
            $clockname = $args["clockname"];
            $clocksize = $args["clocksize"];
            $clockcolor = "FF7F50";

            if ($clocktype == 24) {
                $clocktype = 'clock24';
            } else {
                $clocktype = 'clock';
            }

            // Fix old clock sizing
            if (!in_array($clocksize, array('small', 'large'))) {
                if ($clocksize == '150' || $clocksize == '100') {
                    $clocksize = 'small';
                } else {
                    $clocksize = 'large';
                }
            }

            $output = '
            <div>
                <script async defer id="'.$id.'" src="https://widgets.worldtimeserver.com/Public.ashx?rid='.$id.'&theme=Analog&action='.$clocktype.'&wtsid='.$clocklocation.'&hex='.$clockcolor.'&size='.$clocksize.'"></script>
                <div class="worldtimeserver_clockname">' . $clockname . '</div>
            </div>';
            break;

        case DASHLET_MODE_PREVIEW:
            $output = "<p><img src='" . $imgbase . "preview.png'></p>";
            break;

    }

    return $output;
}


/**
 * @return string
 */
function worldtimeserver_get_location_options()
{
    $output = '<option value="UTC">(UTC/GMT)</option>
    <option value="AF">Afghanistan</option>
    <option value="AL">Albania</option>

    <option value="DZ">Algeria</option>
    <option value="AS">American Samoa</option>
    <option value="AD">Andorra</option>
    <option value="AO">Angola</option>
    <option value="AI">Anguilla</option>
    <option value="AG">Antigua and Barbuda</option>

    <option value="AR-BA">Argentina - Buenos Aires</option>
    <option value="AR-CT">Argentina - Catamarca</option>
    <option value="AR-CC">Argentina - Chaco</option>
    <option value="AR-CH">Argentina - Chubut</option>
    <option value="AR-DF">Argentina - Ciudad de Buenos Aires</option>
    <option value="AR-CB">Argentina - C&#243;rdoba</option>

    <option value="AR-CN">Argentina - Corrientes</option>
    <option value="AR-ER">Argentina - Entre Rios</option>
    <option value="AR-FM">Argentina - Formosa</option>
    <option value="AR-JY">Argentina - Jujuy</option>
    <option value="AR-LP">Argentina - La Pampa</option>
    <option value="AR-LR">Argentina - La Rioja</option>

    <option value="AR-MZ">Argentina - Mendoza</option>
    <option value="AR-MN">Argentina - Misiones</option>
    <option value="AR-NQ">Argentina - Neuqu&#233;n</option>
    <option value="AR-RN">Argentina - Rio Negro</option>
    <option value="AR-SA">Argentina - Salta</option>
    <option value="AR-SJ">Argentina - San Juan</option>

    <option value="AR-SL">Argentina - San Luis</option>
    <option value="AR-SC">Argentina - Santa Cruz</option>
    <option value="AR-SF">Argentina - Santa Fe</option>
    <option value="AR-SE">Argentina - Santiago del Estero</option>
    <option value="AR-TF">Argentina - Tierra del Fuego</option>
    <option value="AR-TM">Argentina - Tucum&#225;n</option>

    <option value="AM">Armenia</option>
    <option value="AW">Aruba</option>
    <option value="AU-ACT">Australia - Australian Capital Territory</option>
    <option value="AU1">Australia - Lord Howe Island</option>
    <option value="AU-NSW">Australia - New South Wales</option>
    <option value="AU3">Australia - New South Wales (exception)</option>

    <option value="AU-NT">Australia - Northern Territory</option>
    <option value="AU-QLD">Australia - Queensland</option>
    <option value="AU-SA">Australia - South Australia</option>
    <option value="AU-TAS">Australia - Tasmania</option>
    <option value="AU-VIC">Australia - Victoria</option>
    <option value="AU-WA">Australia - Western Australia</option>

    <option value="AT">Austria</option>
    <option value="AZ">Azerbaijan</option>
    <option value="BS">Bahamas</option>
    <option value="BH">Bahrain</option>
    <option value="BD">Bangladesh</option>
    <option value="BB">Barbados</option>

    <option value="BY">Belarus</option>
    <option value="BE">Belgium</option>
    <option value="BZ">Belize</option>
    <option value="BJ">Benin</option>
    <option value="BM">Bermuda</option>
    <option value="BT">Bhutan</option>

    <option value="BO">Bolivia</option>
    <option value="BA">Bosnia and Herzegovina</option>
    <option value="BW">Botswana</option>
    <option value="BR-AC">Brazil - Acre</option>
    <option value="BR-AL">Brazil - Alagoas</option>
    <option value="BR-AP">Brazil - Amapa</option>

    <option value="BR-AM">Brazil - Amazonas</option>
    <option value="BR-BA">Brazil - Bahia</option>
    <option value="BR-CE">Brazil - Ceara</option>
    <option value="BR-DF">Brazil - Distrito Federal</option>
    <option value="BR-ES">Brazil - Espirto Santo</option>
    <option value="BR-FN">Brazil - Fernando de Noronha</option>

    <option value="BR-GO">Brazil - Goias</option>
    <option value="BR-MA">Brazil - Maranhao</option>
    <option value="BR-MT">Brazil - Mato Grosso</option>
    <option value="BR-MS">Brazil - Mato Grosso do Sul</option>
    <option value="BR-MG">Brazil - Minas Gerais</option>
    <option value="BR-PA1">Brazil - Para (eastern)</option>

    <option value="BR-PA2">Brazil - Para (western)</option>
    <option value="BR-PB">Brazil - Paraiba</option>
    <option value="BR-PR">Brazil - Parana</option>
    <option value="BR-PE">Brazil - Pernambuco</option>
    <option value="BR-PI">Brazil - Piaui</option>
    <option value="BR-RJ">Brazil - Rio de Janeiro</option>

    <option value="BR-RN">Brazil - Rio Grande do Norte</option>
    <option value="BR-RS">Brazil - Rio Grande do Sul</option>
    <option value="BR-RO">Brazil - Rondonia</option>
    <option value="BR-RR">Brazil - Roraima</option>
    <option value="BR-SC">Brazil - Santa Catarina</option>
    <option value="BR-SP">Brazil - Sao Paulo</option>

    <option value="BR-SE">Brazil - Sergipe</option>
    <option value="BR-TO">Brazil - Tocantins</option>
    <option value="BN">Brunei Darussalam</option>
    <option value="BG">Bulgaria</option>
    <option value="BF">Burkina Faso</option>
    <option value="BI">Burundi</option>

    <option value="KH">Cambodia</option>
    <option value="CM">Cameroon</option>
    <option value="CA-AB">Canada - Alberta</option>
    <option value="CA-BC">Canada - British Columbia</option>
    <option value="CA-BC1">Canada - British Columbia (exception 1)</option>
    <option value="CA-BC2">Canada - British Columbia (exception 2)</option>

    <option value="CA2">Canada - Labrador</option>
    <option value="CA2A">Canada - Labrador (exception)</option>
    <option value="CA-MB">Canada - Manitoba</option>
    <option value="CA-NB">Canada - New Brunswick</option>
    <option value="CA-NF">Canada - Newfoundland</option>
    <option value="CA-NT">Canada - Northwest Territories</option>

    <option value="CA-NS">Canada - Nova Scotia</option>
    <option value="CA-NT2A">Canada - Nunavut - Southampton Island</option>
    <option value="CA-NT2B">Canada - Nunavut (Central)</option>
    <option value="CA-NT2">Canada - Nunavut (Eastern)</option>
    <option value="CA-NT2C">Canada - Nunavut (Mountain)</option>
    <option value="CA-ON">Canada - Ontario</option>

    <option value="CA-ON1">Canada - Ontario (western)</option>
    <option value="CA-PE">Canada - Prince Edward Island</option>
    <option value="CA-QC">Canada - Quebec</option>
    <option value="CA-QC1">Canada - Quebec (far east)</option>
    <option value="CA-SK">Canada - Saskatchewan</option>
    <option value="CA-SK2">Canada - Saskatchewan (exceptions - east)</option>

    <option value="CA-SK1">Canada - Saskatchewan (exceptions - west)</option>
    <option value="CA-YT">Canada - Yukon</option>
    <option value="CV">Cape Verde</option>
    <option value="KY">Cayman Islands</option>
    <option value="CF">Central African Republic</option>
    <option value="TD">Chad</option>

    <option value="CL">Chile</option>
    <option value="CL2">Chile - Easter Island</option>
    <option value="CN">China</option>
    <option value="CX">Christmas Island (Indian Ocean)</option>
    <option value="CC">Cocos (Keeling) Islands</option>
    <option value="CO">Colombia</option>

    <option value="KM">Comoros</option>
    <option value="CG">Congo</option>
    <option value="CD2">Congo, Democratic Republic of - (Eastern)</option>
    <option value="CD">Congo, Democratic Republic of - (Western)</option>
    <option value="CK">Cook Islands</option>
    <option value="CR">Costa Rica</option>

    <option value="CI">Cote D\'Ivoire</option>
    <option value="HR">Croatia</option>
    <option value="CU">Cuba</option>
    <option value="CY">Cyprus</option>
    <option value="CZ">Czech Republic</option>
    <option value="DK">Denmark</option>

    <option value="DJ">Djibouti</option>
    <option value="DM">Dominica</option>
    <option value="DO">Dominican Republic</option>
    <option value="EC">Ecuador</option>
    <option value="EC2">Ecuador - Galapagos Islands</option>
    <option value="EG">Egypt</option>

    <option value="SV">El Salvador</option>
    <option value="GQ">Equatorial Guinea</option>
    <option value="ER">Eritrea</option>
    <option value="EE">Estonia</option>
    <option value="ET">Ethiopia</option>
    <option value="FK">Falkland Islands (Malvinas)</option>

    <option value="FO">Faroe Islands</option>
    <option value="FJ">Fiji</option>
    <option value="FI">Finland</option>
    <option value="FR">France</option>
    <option value="GF">French Guiana</option>
    <option value="PF2A">French Polynesia - Austral Islands</option>

    <option value="PF3">French Polynesia - Gambier Islands</option>
    <option value="PF1">French Polynesia - Marquesas Islands</option>
    <option value="PF">French Polynesia - Society Islands (including Tahiti)</option>
    <option value="PF2B">French Polynesia - Tuamotu Archipelago</option>
    <option value="GA">Gabon</option>
    <option value="GM">Gambia</option>

    <option value="GE">Georgia</option>
    <option value="DE">Germany</option>
    <option value="GH">Ghana</option>
    <option value="GI">Gibraltar</option>
    <option value="GR">Greece</option>
    <option value="GL4">Greenland - Danmarkshavn</option>

    <option value="GL">Greenland - Greenland</option>
    <option value="GL3">Greenland - Ittoqqortoormiit</option>
    <option value="GL2">Greenland - Pituffik</option>
    <option value="GD">Grenada</option>
    <option value="GP">Guadeloupe</option>
    <option value="GU">Guam</option>

    <option value="GT">Guatemala</option>
    <option value="GN">Guinea</option>
    <option value="GW">Guinea-Bissau</option>
    <option value="GY">Guyana</option>
    <option value="HT">Haiti</option>
    <option value="VA">Holy See</option>

    <option value="HN">Honduras</option>
    <option value="HK">Hong Kong</option>
    <option value="HU">Hungary</option>
    <option value="IS">Iceland</option>
    <option value="IN">India</option>
    <option value="ID2">Indonesia - (Central)</option>

    <option value="ID3">Indonesia - (Eastern)</option>
    <option value="ID">Indonesia - (Western)</option>
    <option value="IR">Iran, Islamic Republic of</option>
    <option value="IQ">Iraq</option>
    <option value="IE">Ireland</option>
    <option value="IL">Israel</option>

    <option value="IT">Italy</option>
    <option value="JM">Jamaica</option>
    <option value="JP">Japan</option>
    <option value="UM1">Johnston Atoll (U.S.)</option>
    <option value="JO">Jordan</option>
    <option value="KZ">Kazakhstan - (Eastern)</option>

    <option value="KZ1">Kazakhstan - (Western)</option>
    <option value="KE">Kenya</option>
    <option value="KI">Kiribati - Gilbert Islands</option>
    <option value="KI2">Kiribati - Line Islands</option>
    <option value="KI3">Kiribati - Phoenix Islands</option>
    <option value="KP">Korea, Democratic People\'s Republic of</option>

    <option value="KR">Korea, Republic of</option>
    <option value="KW">Kuwait</option>
    <option value="KG">Kyrgyzstan</option>
    <option value="LA">Lao People\'s Democratic Republic</option>
    <option value="LV">Latvia</option>
    <option value="LB">Lebanon</option>

    <option value="LS">Lesotho</option>
    <option value="LR">Liberia</option>
    <option value="LY">Libyan Arab Jamahiriya</option>
    <option value="LI">Liechtenstein</option>
    <option value="LT">Lithuania</option>
    <option value="LU">Luxembourg</option>

    <option value="MO">Macao</option>
    <option value="MK">Macedonia, The Former Yugoslav Republic Of</option>
    <option value="MG">Madagascar</option>
    <option value="MW">Malawi</option>
    <option value="MY">Malaysia</option>
    <option value="MV">Maldives</option>

    <option value="ML">Mali</option>
    <option value="MT">Malta</option>
    <option value="MH">Marshall Islands</option>
    <option value="MQ">Martinique</option>
    <option value="MR">Mauritania</option>
    <option value="MU">Mauritius</option>

    <option value="YT">Mayotte</option>
    <option value="MX">Mexico - (South, Central, and Eastern)</option>
    <option value="MX3">Mexico - Baja California Norte</option>
    <option value="MX2">Mexico - Baja California Sur</option>
    <option value="MX2-3">Mexico - Chihuahua</option>
    <option value="MX2-1">Mexico - Nayarit</option>

    <option value="MX2-2">Mexico - Sinaloa</option>
    <option value="MX2A">Mexico - Sonora</option>
    <option value="FM">Micronesia, Federated States Of - Kosrae, Pohnpei</option>
    <option value="FM1">Micronesia, Federated States Of - Yap, Chuuk</option>
    <option value="UM2">Midway Islands (U.S.)</option>
    <option value="MD">Moldova</option>

    <option value="MC">Monaco</option>
    <option value="MN">Mongolia - (Central and Eastern)</option>
    <option value="MN1">Mongolia - (Western)</option>
    <option value="ME">Montenegro</option>
    <option value="MS">Montserrat</option>
    <option value="MA">Morocco</option>

    <option value="MZ">Mozambique</option>
    <option value="MM">Myanmar</option>
    <option value="NA">Namibia</option>
    <option value="NR">Nauru</option>
    <option value="NP">Nepal</option>
    <option value="NL">Netherlands</option>

    <option value="AN">Netherlands Antilles - Bonaire, Cura&#231;ao, Saba, Sint Eustatius, Sint Maarten</option>
    <option value="NC">New Caledonia</option>
    <option value="NZ">New Zealand</option>
    <option value="NZ2">New Zealand - Chatham Islands</option>
    <option value="NI">Nicaragua</option>
    <option value="NE">Niger</option>

    <option value="NG">Nigeria</option>
    <option value="NU">Niue</option>
    <option value="NF">Norfolk Island</option>
    <option value="MP">Northern Mariana Islands</option>
    <option value="NO">Norway</option>
    <option value="OM">Oman</option>

    <option value="PK">Pakistan</option>
    <option value="PW">Palau</option>
    <option value="PS">Palestinian Territory</option>
    <option value="PA">Panama</option>
    <option value="PG">Papua New Guinea</option>
    <option value="PY">Paraguay</option>

    <option value="PE">Peru</option>
    <option value="PH">Philippines</option>
    <option value="PN">Pitcairn</option>
    <option value="PL">Poland</option>
    <option value="PT">Portugal</option>
    <option value="PT2">Portugal - Azores</option>

    <option value="PT1">Portugal - Madeira Islands</option>
    <option value="PR">Puerto Rico</option>
    <option value="QA">Qatar</option>
    <option value="RE">Reunion</option>
    <option value="RO">Romania</option>
    <option value="RU-AD">Russia - Adygea</option>

    <option value="RU-AL">Russia - Altai Republic</option>
    <option value="RU-ALT">Russia - Altaskiy Kray</option>
    <option value="RU-AMU">Russia - Amur</option>
    <option value="RU-ARK">Russia - Arkhangel\'</option>
    <option value="RU-AST">Russia - Astrakhan\'</option>
    <option value="RU-BA">Russia - Bashkortostan</option>

    <option value="RU-BEL">Russia - Belgorod</option>
    <option value="RU-BRY">Russia - Bryansk</option>
    <option value="RU-BU">Russia - Buryatia</option>
    <option value="RU-CE">Russia - Chechnya</option>
    <option value="RU-CHE">Russia - Chelyabinsk</option>
    <option value="RU-CHU">Russia - Chukot</option>

    <option value="RU-CU">Russia - Chuvashia</option>
    <option value="RU-DA">Russia - Dagestan</option>
    <option value="RU-IN">Russia - Ingushetia</option>
    <option value="RU-IRK">Russia - Irkutsk</option>
    <option value="RU-IVA">Russia - Ivanovo</option>
    <option value="RU-YEV">Russia - Jewish Autonomous Oblast\'</option>

    <option value="RU-KB">Russia - Kabardino-Balkaria</option>
    <option value="RU-KGD">Russia - Kaliningrad</option>
    <option value="RU-KL">Russia - Kalmykia</option>
    <option value="RU-KLU">Russia - Kaluga</option>
    <option value="RU-KAM">Russia - Kamchatka</option>
    <option value="RU-KC">Russia - Karachay-Cherkessia</option>

    <option value="RU-KR">Russia - Karelia</option>
    <option value="RU-KEM">Russia - Kemerovo</option>
    <option value="RU-KHA">Russia - Khabarovsk</option>
    <option value="RU-KK">Russia - Khakassia</option>
    <option value="RU-KHM">Russia - Khanty-Mansi</option>
    <option value="RU-KIR">Russia - Kirov</option>

    <option value="RU-KO">Russia - Komi</option>
    <option value="RU-KOS">Russia - Kostroma</option>
    <option value="RU-KDA">Russia - Krasnodar</option>
    <option value="RU-KYA">Russia - Krasnoyarsk</option>
    <option value="RU-KGN">Russia - Kurgan</option>
    <option value="RU-KRS">Russia - Kursk</option>

    <option value="RU-LEN">Russia - Leningradskaya Oblast\'</option>
    <option value="RU-LIP">Russia - Lipetsk</option>
    <option value="RU-MAG">Russia - Magadan</option>
    <option value="RU-ME">Russia - Mari El</option>
    <option value="RU-MO">Russia - Mordovia</option>
    <option value="RU-MOW">Russia - Moscow City</option>

    <option value="RU-MOS">Russia - Moskva</option>
    <option value="RU-MUR">Russia - Murmansk</option>
    <option value="RU-NEN">Russia - Nenets</option>
    <option value="RU-NIZ">Russia - Nizhniy Novgorod</option>
    <option value="RU-SE">Russia - North Ossetia-Alania</option>
    <option value="RU-NGR">Russia - Novgorod</option>

    <option value="RU-NVS">Russia - Novosibirsk</option>
    <option value="RU-OMS">Russia - Omsk</option>
    <option value="RU-ORL">Russia - Orel</option>
    <option value="RU-ORE">Russia - Orenburg</option>
    <option value="RU-PNZ">Russia - Penza</option>
    <option value="RU-PER">Russia - Perm</option>

    <option value="RU-PRI">Russia - Primorskiy</option>
    <option value="RU-PSK">Russia - Pskov</option>
    <option value="RU-ROS">Russia - Rostov</option>
    <option value="RU-RYA">Russia - Ryazan\'</option>
    <option value="RU-SA2">Russia - Sakha (Central)</option>
    <option value="RU-SA3">Russia - Sakha (Eastern)</option>

    <option value="RU-SA">Russia - Sakha (Western)</option>
    <option value="RU-SAK">Russia - Sakhalin</option>
    <option value="RU-SAK2">Russia - Sakhalin (Kuril Islands)</option>
    <option value="RU-SAM">Russia - Samara</option>
    <option value="RU-SAR">Russia - Saratov</option>
    <option value="RU-SMO">Russia - Smolensk</option>

    <option value="RU-SPE">Russia - St. Petersburg City</option>
    <option value="RU-STA">Russia - Stavropol</option>
    <option value="RU-SVE">Russia - Sverdlovsk</option>
    <option value="RU-TAM">Russia - Tambov</option>
    <option value="RU-TA">Russia - Tatarstan</option>
    <option value="RU-TOM">Russia - Tomsk</option>

    <option value="RU-TUL">Russia - Tula</option>
    <option value="RU-TY">Russia - Tuva</option>
    <option value="RU-TVE">Russia - Tver\'</option>
    <option value="RU-TYU">Russia - Tyumen\'</option>
    <option value="RU-UD">Russia - Udmurtia</option>
    <option value="RU-ULY">Russia - Ul\'yanovsk</option>

    <option value="RU-VLA">Russia - Vladimir</option>
    <option value="RU-VGG">Russia - Volgograd</option>
    <option value="RU-VLG">Russia - Vologda</option>
    <option value="RU-VOR">Russia - Voronezh</option>
    <option value="RU-YAN">Russia - Yamalo-Nenets</option>
    <option value="RU-YAR">Russia - Yaroslavl\'</option>

    <option value="RU-CHI">Russia - Zabaykalsky</option>
    <option value="RW">Rwanda</option>
    <option value="BL">Saint Barthelemy</option>
    <option value="SH">Saint Helena</option>
    <option value="KN">Saint Kitts and Nevis</option>
    <option value="LC">Saint Lucia</option>

    <option value="MF">Saint Martin</option>
    <option value="PM">Saint Pierre and Miquelon</option>
    <option value="VC">Saint Vincent and The Grenadines</option>
    <option value="WS">Samoa</option>
    <option value="SM">San Marino</option>
    <option value="ST">Sao Tome and Principe</option>

    <option value="SA">Saudi Arabia</option>
    <option value="SN">Senegal</option>
    <option value="RS">Serbia</option>
    <option value="SC">Seychelles</option>
    <option value="SL">Sierra Leone</option>
    <option value="SG">Singapore</option>

    <option value="SK">Slovakia</option>
    <option value="SI">Slovenia</option>
    <option value="SB">Solomon Islands</option>
    <option value="SO">Somalia</option>
    <option value="ZA">South Africa</option>
    <option value="ES2">Spain - Canary Islands</option>

    <option value="ES">Spain - Mainland, Baleares, Melilla, Ceuta</option>
    <option value="LK">Sri Lanka</option>
    <option value="SD">Sudan</option>
    <option value="SR">Suriname</option>
    <option value="SJ">Svalbard and Jan Mayen</option>
    <option value="SZ">Swaziland</option>

    <option value="SE">Sweden</option>
    <option value="CH">Switzerland</option>
    <option value="SY">Syrian Arab Republic</option>
    <option value="TW">Taiwan</option>
    <option value="TJ">Tajikistan</option>
    <option value="TZ">Tanzania, United Republic of</option>

    <option value="TH">Thailand</option>
    <option value="TL">Timor-Leste</option>
    <option value="TG">Togo</option>
    <option value="TK">Tokelau</option>
    <option value="TO">Tonga</option>
    <option value="TT">Trinidad and Tobago</option>

    <option value="TN">Tunisia</option>
    <option value="TR">Turkey</option>
    <option value="TM">Turkmenistan</option>
    <option value="TC">Turks and Caicos Islands</option>
    <option value="TV">Tuvalu</option>
    <option value="UG">Uganda</option>

    <option value="UA">Ukraine</option>
    <option value="AE">United Arab Emirates</option>
    <option value="GB">United Kingdom</option>
    <option value="US-AL">United States - Alabama</option>
    <option value="US-AK">United States - Alaska</option>
    <option value="US-AK1">United States - Alaska (Aleutian Islands)</option>

    <option value="US-AZ">United States - Arizona</option>
    <option value="US-AZ1">United States - Arizona (Navajo Reservation)</option>
    <option value="US-AR">United States - Arkansas</option>
    <option value="US-CA">United States - California</option>
    <option value="US-CO">United States - Colorado</option>
    <option value="US-CT">United States - Connecticut</option>

    <option value="US-DE">United States - Delaware</option>
    <option value="US-DC">United States - District of Columbia</option>
    <option value="US-FL">United States - Florida</option>
    <option value="US-FL1">United States - Florida (far west)</option>
    <option value="US-GA">United States - Georgia</option>
    <option value="US-HI">United States - Hawaii</option>

    <option value="US-ID1">United States - Idaho (northern)</option>
    <option value="US-ID">United States - Idaho (southern)</option>
    <option value="US-IL">United States - Illinois</option>
    <option value="US-IN">United States - Indiana</option>
    <option value="US-IN1">United States - Indiana (far west)</option>
    <option value="US-IA">United States - Iowa</option>

    <option value="US-KS">United States - Kansas</option>
    <option value="US-KS1">United States - Kansas (exception)</option>
    <option value="US-KY">United States - Kentucky (eastern)</option>
    <option value="US-KY1">United States - Kentucky (western)</option>
    <option value="US-LA">United States - Louisiana</option>
    <option value="US-ME">United States - Maine</option>

    <option value="US-MD">United States - Maryland</option>
    <option value="US-MA">United States - Massachusetts</option>
    <option value="US-MI">United States - Michigan</option>
    <option value="US-MI1">United States - Michigan (exception)</option>
    <option selected="selected" value="US-MN">United States - Minnesota</option>
    <option value="US-MS">United States - Mississippi</option>

    <option value="US-MO">United States - Missouri</option>
    <option value="US-MT">United States - Montana</option>
    <option value="US-NE">United States - Nebraska</option>
    <option value="US-NE1">United States - Nebraska (western)</option>
    <option value="US-NV">United States - Nevada</option>
    <option value="US-NH">United States - New Hampshire</option>

    <option value="US-NJ">United States - New Jersey</option>
    <option value="US-NM">United States - New Mexico</option>
    <option value="US-NY">United States - New York</option>
    <option value="US-NC">United States - North Carolina</option>
    <option value="US-ND">United States - North Dakota</option>
    <option value="US-ND1">United States - North Dakota (western)</option>

    <option value="US-OH">United States - Ohio</option>
    <option value="US-OK">United States - Oklahoma</option>
    <option value="US-OR">United States - Oregon</option>
    <option value="US-OR1">United States - Oregon (exception)</option>
    <option value="US-PA">United States - Pennsylvania</option>
    <option value="US-RI">United States - Rhode Island</option>

    <option value="US-SC">United States - South Carolina</option>
    <option value="US-SD">United States - South Dakota (eastern)</option>
    <option value="US-SD1">United States - South Dakota (western)</option>
    <option value="US-TN1">United States - Tennessee (eastern)</option>
    <option value="US-TN">United States - Tennessee (western)</option>
    <option value="US-TX">United States - Texas</option>

    <option value="US-TX1">United States - Texas (far west)</option>
    <option value="US-UT">United States - Utah</option>
    <option value="US-VT">United States - Vermont</option>
    <option value="US-VA">United States - Virginia</option>
    <option value="US-WA">United States - Washington</option>
    <option value="US-WV">United States - West Virginia</option>

    <option value="US-WI">United States - Wisconsin</option>
    <option value="US-WY">United States - Wyoming</option>
    <option value="UY">Uruguay</option>
    <option value="UZ">Uzbekistan</option>
    <option value="VU">Vanuatu</option>
    <option value="VE">Venezuela</option>

    <option value="VN">Viet Nam</option>
    <option value="VG">Virgin Islands (British)</option>
    <option value="VI">Virgin Islands (U.S.)</option>
    <option value="UM3">Wake Island (U.S.)</option>
    <option value="WF">Wallis and Futuna</option>
    <option value="EH">Western Sahara</option>

    <option value="YE">Yemen</option>
    <option value="ZM">Zambia</option>
    <option value="ZW">Zimbabwe</option>';
    return $output;
}

