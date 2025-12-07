<x-guest-layout>
    <div class="w-full max-w-2xl bg-white/95 rounded-lg shadow-lg p-8">
        <h1 class="text-center mb-6 font-medium text-xl">Store App Register</h1>
        
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('storeowner.register') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- First Name -->
            <div class="flex items-start gap-4">
                <label for="firstname" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    First name<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="firstname" class="block w-full" type="text" name="firstname" :value="old('firstname')" required autofocus />
                    <x-input-error :messages="$errors->get('firstname')" class="mt-2" />
                </div>
            </div>

            <!-- Last Name -->
            <div class="flex items-start gap-4">
                <label for="lastname" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Last name<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="lastname" class="block w-full" type="text" name="lastname" :value="old('lastname')" required />
                    <x-input-error :messages="$errors->get('lastname')" class="mt-2" />
                </div>
            </div>

            <!-- Username -->
            <div class="flex items-start gap-4">
                <label for="username" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Username<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="username" class="block w-full" type="text" name="username" :value="old('username')" required />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>
            </div>

            <!-- Email -->
            <div class="flex items-start gap-4">
                <label for="emailid" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Email<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="emailid" class="block w-full" type="email" name="emailid" :value="old('emailid')" required />
                    <x-input-error :messages="$errors->get('emailid')" class="mt-2" />
                </div>
            </div>

            <!-- Password -->
            <div class="flex items-start gap-4">
                <label for="password" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Password<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="flex items-start gap-4">
                <label for="password_confirmation" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Confirm Password<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <!-- Profile Photo -->
            <div class="flex items-start gap-4">
                <label for="profile_photo" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Profile Photo<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <input id="profile_photo" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500" type="file" name="profile_photo" accept="image/*" />
                    <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
                </div>
            </div>

            <!-- Date of Birth -->
            <div class="flex items-start gap-4">
                <label for="dateofbirth" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Date of Birth<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="dateofbirth" class="block w-full" type="date" name="dateofbirth" :value="old('dateofbirth')" required />
                    <x-input-error :messages="$errors->get('dateofbirth')" class="mt-2" />
                </div>
            </div>

            <!-- Country -->
            <div class="flex items-start gap-4">
                <label for="country" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Country<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <select id="country" name="country" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">Select Country</option>
                    <option value="Ireland" {{ old('country') == 'Ireland' ? 'selected' : '' }}>Ireland (+353)</option>
                    <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>UK (+44)</option>
                    <option value="USA" {{ old('country', 'USA') == 'USA' ? 'selected' : '' }}>USA (+1)</option>
                    <option disabled>Other Countries</option>
                    <option value="Algeria" {{ old('country') == 'Algeria' ? 'selected' : '' }}>Algeria (+213)</option>
                    <option value="Andorra" {{ old('country') == 'Andorra' ? 'selected' : '' }}>Andorra (+376)</option>
                    <option value="Angola" {{ old('country') == 'Angola' ? 'selected' : '' }}>Angola (+244)</option>
                    <option value="Anguilla" {{ old('country') == 'Anguilla' ? 'selected' : '' }}>Anguilla (+1264)</option>
                    <option value="Antigua & Barbuda" {{ old('country') == 'Antigua & Barbuda' ? 'selected' : '' }}>Antigua & Barbuda (+1268)</option>
                    <option value="Argentina" {{ old('country') == 'Argentina' ? 'selected' : '' }}>Argentina (+54)</option>
                    <option value="Armenia" {{ old('country') == 'Armenia' ? 'selected' : '' }}>Armenia (+374)</option>
                    <option value="Aruba" {{ old('country') == 'Aruba' ? 'selected' : '' }}>Aruba (+297)</option>
                    <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia (+61)</option>
                    <option value="Austria" {{ old('country') == 'Austria' ? 'selected' : '' }}>Austria (+43)</option>
                    <option value="Azerbaijan" {{ old('country') == 'Azerbaijan' ? 'selected' : '' }}>Azerbaijan (+994)</option>
                    <option value="Bahamas" {{ old('country') == 'Bahamas' ? 'selected' : '' }}>Bahamas (+1242)</option>
                    <option value="Bahrain" {{ old('country') == 'Bahrain' ? 'selected' : '' }}>Bahrain (+973)</option>
                    <option value="Bangladesh" {{ old('country') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh (+880)</option>
                    <option value="Barbados" {{ old('country') == 'Barbados' ? 'selected' : '' }}>Barbados (+1246)</option>
                    <option value="Belarus" {{ old('country') == 'Belarus' ? 'selected' : '' }}>Belarus (+375)</option>
                    <option value="Belgium" {{ old('country') == 'Belgium' ? 'selected' : '' }}>Belgium (+32)</option>
                    <option value="Belize" {{ old('country') == 'Belize' ? 'selected' : '' }}>Belize (+501)</option>
                    <option value="Benin" {{ old('country') == 'Benin' ? 'selected' : '' }}>Benin (+229)</option>
                    <option value="Bermuda" {{ old('country') == 'Bermuda' ? 'selected' : '' }}>Bermuda (+1441)</option>
                    <option value="Bhutan" {{ old('country') == 'Bhutan' ? 'selected' : '' }}>Bhutan (+975)</option>
                    <option value="Bolivia" {{ old('country') == 'Bolivia' ? 'selected' : '' }}>Bolivia (+591)</option>
                    <option value="Bosnia Herzegovina" {{ old('country') == 'Bosnia Herzegovina' ? 'selected' : '' }}>Bosnia Herzegovina (+387)</option>
                    <option value="Botswana" {{ old('country') == 'Botswana' ? 'selected' : '' }}>Botswana (+267)</option>
                    <option value="Brazil" {{ old('country') == 'Brazil' ? 'selected' : '' }}>Brazil (+55)</option>
                    <option value="Brunei" {{ old('country') == 'Brunei' ? 'selected' : '' }}>Brunei (+673)</option>
                    <option value="Bulgaria" {{ old('country') == 'Bulgaria' ? 'selected' : '' }}>Bulgaria (+359)</option>
                    <option value="Burkina Faso" {{ old('country') == 'Burkina Faso' ? 'selected' : '' }}>Burkina Faso (+226)</option>
                    <option value="Burundi" {{ old('country') == 'Burundi' ? 'selected' : '' }}>Burundi (+257)</option>
                    <option value="Cambodia" {{ old('country') == 'Cambodia' ? 'selected' : '' }}>Cambodia (+855)</option>
                    <option value="Cameroon" {{ old('country') == 'Cameroon' ? 'selected' : '' }}>Cameroon (+237)</option>
                    <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada (+1)</option>
                    <option value="Cape Verde Islands" {{ old('country') == 'Cape Verde Islands' ? 'selected' : '' }}>Cape Verde Islands (+238)</option>
                    <option value="Cayman Islands" {{ old('country') == 'Cayman Islands' ? 'selected' : '' }}>Cayman Islands (+1345)</option>
                    <option value="Central African Republic" {{ old('country') == 'Central African Republic' ? 'selected' : '' }}>Central African Republic (+236)</option>
                    <option value="Chile" {{ old('country') == 'Chile' ? 'selected' : '' }}>Chile (+56)</option>
                    <option value="China" {{ old('country') == 'China' ? 'selected' : '' }}>China (+86)</option>
                    <option value="Colombia" {{ old('country') == 'Colombia' ? 'selected' : '' }}>Colombia (+57)</option>
                    <option value="Comoros" {{ old('country') == 'Comoros' ? 'selected' : '' }}>Comoros (+269)</option>
                    <option value="Congo" {{ old('country') == 'Congo' ? 'selected' : '' }}>Congo (+242)</option>
                    <option value="Cook Islands" {{ old('country') == 'Cook Islands' ? 'selected' : '' }}>Cook Islands (+682)</option>
                    <option value="Costa Rica" {{ old('country') == 'Costa Rica' ? 'selected' : '' }}>Costa Rica (+506)</option>
                    <option value="Croatia" {{ old('country') == 'Croatia' ? 'selected' : '' }}>Croatia (+385)</option>
                    <option value="Cyprus - North" {{ old('country') == 'Cyprus - North' ? 'selected' : '' }}>Cyprus - North (+90)</option>
                    <option value="Cyprus - South" {{ old('country') == 'Cyprus - South' ? 'selected' : '' }}>Cyprus - South (+357)</option>
                    <option value="Czech Republic" {{ old('country') == 'Czech Republic' ? 'selected' : '' }}>Czech Republic (+420)</option>
                    <option value="Denmark" {{ old('country') == 'Denmark' ? 'selected' : '' }}>Denmark (+45)</option>
                    <option value="Djibouti" {{ old('country') == 'Djibouti' ? 'selected' : '' }}>Djibouti (+253)</option>
                    <option value="Dominica" {{ old('country') == 'Dominica' ? 'selected' : '' }}>Dominica (+1809)</option>
                    <option value="Dominican Republic" {{ old('country') == 'Dominican Republic' ? 'selected' : '' }}>Dominican Republic (+1809)</option>
                    <option value="Ecuador" {{ old('country') == 'Ecuador' ? 'selected' : '' }}>Ecuador (+593)</option>
                    <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>Egypt (+20)</option>
                    <option value="El Salvador" {{ old('country') == 'El Salvador' ? 'selected' : '' }}>El Salvador (+503)</option>
                    <option value="Equatorial Guinea" {{ old('country') == 'Equatorial Guinea' ? 'selected' : '' }}>Equatorial Guinea (+240)</option>
                    <option value="Eritrea" {{ old('country') == 'Eritrea' ? 'selected' : '' }}>Eritrea (+291)</option>
                    <option value="Estonia" {{ old('country') == 'Estonia' ? 'selected' : '' }}>Estonia (+372)</option>
                    <option value="Ethiopia" {{ old('country') == 'Ethiopia' ? 'selected' : '' }}>Ethiopia (+251)</option>
                    <option value="Falkland Islands" {{ old('country') == 'Falkland Islands' ? 'selected' : '' }}>Falkland Islands (+500)</option>
                    <option value="Faroe Islands" {{ old('country') == 'Faroe Islands' ? 'selected' : '' }}>Faroe Islands (+298)</option>
                    <option value="Fiji" {{ old('country') == 'Fiji' ? 'selected' : '' }}>Fiji (+679)</option>
                    <option value="Finland" {{ old('country') == 'Finland' ? 'selected' : '' }}>Finland (+358)</option>
                    <option value="France" {{ old('country') == 'France' ? 'selected' : '' }}>France (+33)</option>
                    <option value="French Guiana" {{ old('country') == 'French Guiana' ? 'selected' : '' }}>French Guiana (+594)</option>
                    <option value="French Polynesia" {{ old('country') == 'French Polynesia' ? 'selected' : '' }}>French Polynesia (+689)</option>
                    <option value="Gabon" {{ old('country') == 'Gabon' ? 'selected' : '' }}>Gabon (+241)</option>
                    <option value="Gambia" {{ old('country') == 'Gambia' ? 'selected' : '' }}>Gambia (+220)</option>
                    <option value="Georgia" {{ old('country') == 'Georgia' ? 'selected' : '' }}>Georgia (+7880)</option>
                    <option value="Germany" {{ old('country') == 'Germany' ? 'selected' : '' }}>Germany (+49)</option>
                    <option value="Ghana" {{ old('country') == 'Ghana' ? 'selected' : '' }}>Ghana (+233)</option>
                    <option value="Gibraltar" {{ old('country') == 'Gibraltar' ? 'selected' : '' }}>Gibraltar (+350)</option>
                    <option value="Greece" {{ old('country') == 'Greece' ? 'selected' : '' }}>Greece (+30)</option>
                    <option value="Greenland" {{ old('country') == 'Greenland' ? 'selected' : '' }}>Greenland (+299)</option>
                    <option value="Grenada" {{ old('country') == 'Grenada' ? 'selected' : '' }}>Grenada (+1473)</option>
                    <option value="Guadeloupe" {{ old('country') == 'Guadeloupe' ? 'selected' : '' }}>Guadeloupe (+590)</option>
                    <option value="Guam" {{ old('country') == 'Guam' ? 'selected' : '' }}>Guam (+671)</option>
                    <option value="Guatemala" {{ old('country') == 'Guatemala' ? 'selected' : '' }}>Guatemala (+502)</option>
                    <option value="Guinea" {{ old('country') == 'Guinea' ? 'selected' : '' }}>Guinea (+224)</option>
                    <option value="Guinea - Bissau" {{ old('country') == 'Guinea - Bissau' ? 'selected' : '' }}>Guinea - Bissau (+245)</option>
                    <option value="Guyana" {{ old('country') == 'Guyana' ? 'selected' : '' }}>Guyana (+592)</option>
                    <option value="Haiti" {{ old('country') == 'Haiti' ? 'selected' : '' }}>Haiti (+509)</option>
                    <option value="Honduras" {{ old('country') == 'Honduras' ? 'selected' : '' }}>Honduras (+504)</option>
                    <option value="Hong Kong" {{ old('country') == 'Hong Kong' ? 'selected' : '' }}>Hong Kong (+852)</option>
                    <option value="Hungary" {{ old('country') == 'Hungary' ? 'selected' : '' }}>Hungary (+36)</option>
                    <option value="Iceland" {{ old('country') == 'Iceland' ? 'selected' : '' }}>Iceland (+354)</option>
                    <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India (+91)</option>
                    <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>Indonesia (+62)</option>
                    <option value="Iran" {{ old('country') == 'Iran' ? 'selected' : '' }}>Iran (+98)</option>
                    <option value="Iraq" {{ old('country') == 'Iraq' ? 'selected' : '' }}>Iraq (+964)</option>
                    <option value="Israel" {{ old('country') == 'Israel' ? 'selected' : '' }}>Israel (+972)</option>
                    <option value="Italy" {{ old('country') == 'Italy' ? 'selected' : '' }}>Italy (+39)</option>
                    <option value="Jamaica" {{ old('country') == 'Jamaica' ? 'selected' : '' }}>Jamaica (+1876)</option>
                    <option value="Japan" {{ old('country') == 'Japan' ? 'selected' : '' }}>Japan (+81)</option>
                    <option value="Jordan" {{ old('country') == 'Jordan' ? 'selected' : '' }}>Jordan (+962)</option>
                    <option value="Kazakhstan" {{ old('country') == 'Kazakhstan' ? 'selected' : '' }}>Kazakhstan (+7)</option>
                    <option value="Kenya" {{ old('country') == 'Kenya' ? 'selected' : '' }}>Kenya (+254)</option>
                    <option value="Kiribati" {{ old('country') == 'Kiribati' ? 'selected' : '' }}>Kiribati (+686)</option>
                    <option value="Korea - North" {{ old('country') == 'Korea - North' ? 'selected' : '' }}>Korea - North (+850)</option>
                    <option value="Korea - South" {{ old('country') == 'Korea - South' ? 'selected' : '' }}>Korea - South (+82)</option>
                    <option value="Kuwait" {{ old('country') == 'Kuwait' ? 'selected' : '' }}>Kuwait (+965)</option>
                    <option value="Kyrgyzstan" {{ old('country') == 'Kyrgyzstan' ? 'selected' : '' }}>Kyrgyzstan (+996)</option>
                    <option value="Laos" {{ old('country') == 'Laos' ? 'selected' : '' }}>Laos (+856)</option>
                    <option value="Latvia" {{ old('country') == 'Latvia' ? 'selected' : '' }}>Latvia (+371)</option>
                    <option value="Lebanon" {{ old('country') == 'Lebanon' ? 'selected' : '' }}>Lebanon (+961)</option>
                    <option value="Lesotho" {{ old('country') == 'Lesotho' ? 'selected' : '' }}>Lesotho (+266)</option>
                    <option value="Liberia" {{ old('country') == 'Liberia' ? 'selected' : '' }}>Liberia (+231)</option>
                    <option value="Libya" {{ old('country') == 'Libya' ? 'selected' : '' }}>Libya (+218)</option>
                    <option value="Liechtenstein" {{ old('country') == 'Liechtenstein' ? 'selected' : '' }}>Liechtenstein (+417)</option>
                    <option value="Lithuania" {{ old('country') == 'Lithuania' ? 'selected' : '' }}>Lithuania (+370)</option>
                    <option value="Luxembourg" {{ old('country') == 'Luxembourg' ? 'selected' : '' }}>Luxembourg (+352)</option>
                    <option value="Macao" {{ old('country') == 'Macao' ? 'selected' : '' }}>Macao (+853)</option>
                    <option value="Macedonia" {{ old('country') == 'Macedonia' ? 'selected' : '' }}>Macedonia (+389)</option>
                    <option value="Madagascar" {{ old('country') == 'Madagascar' ? 'selected' : '' }}>Madagascar (+261)</option>
                    <option value="Malawi" {{ old('country') == 'Malawi' ? 'selected' : '' }}>Malawi (+265)</option>
                    <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia (+60)</option>
                    <option value="Maldives" {{ old('country') == 'Maldives' ? 'selected' : '' }}>Maldives (+960)</option>
                    <option value="Mali" {{ old('country') == 'Mali' ? 'selected' : '' }}>Mali (+223)</option>
                    <option value="Malta" {{ old('country') == 'Malta' ? 'selected' : '' }}>Malta (+356)</option>
                    <option value="Marshall Islands" {{ old('country') == 'Marshall Islands' ? 'selected' : '' }}>Marshall Islands (+692)</option>
                    <option value="Martinique" {{ old('country') == 'Martinique' ? 'selected' : '' }}>Martinique (+596)</option>
                    <option value="Mauritania" {{ old('country') == 'Mauritania' ? 'selected' : '' }}>Mauritania (+222)</option>
                    <option value="Mayotte" {{ old('country') == 'Mayotte' ? 'selected' : '' }}>Mayotte (+269)</option>
                    <option value="Mexico" {{ old('country') == 'Mexico' ? 'selected' : '' }}>Mexico (+52)</option>
                    <option value="Micronesia" {{ old('country') == 'Micronesia' ? 'selected' : '' }}>Micronesia (+691)</option>
                    <option value="Moldova" {{ old('country') == 'Moldova' ? 'selected' : '' }}>Moldova (+373)</option>
                    <option value="Monaco" {{ old('country') == 'Monaco' ? 'selected' : '' }}>Monaco (+377)</option>
                    <option value="Mongolia" {{ old('country') == 'Mongolia' ? 'selected' : '' }}>Mongolia (+976)</option>
                    <option value="Montserrat" {{ old('country') == 'Montserrat' ? 'selected' : '' }}>Montserrat (+1664)</option>
                    <option value="Morocco" {{ old('country') == 'Morocco' ? 'selected' : '' }}>Morocco (+212)</option>
                    <option value="Mozambique" {{ old('country') == 'Mozambique' ? 'selected' : '' }}>Mozambique (+258)</option>
                    <option value="Myanmar" {{ old('country') == 'Myanmar' ? 'selected' : '' }}>Myanmar (+95)</option>
                    <option value="Namibia" {{ old('country') == 'Namibia' ? 'selected' : '' }}>Namibia (+264)</option>
                    <option value="Nauru" {{ old('country') == 'Nauru' ? 'selected' : '' }}>Nauru (+674)</option>
                    <option value="Nepal" {{ old('country') == 'Nepal' ? 'selected' : '' }}>Nepal (+977)</option>
                    <option value="Netherlands" {{ old('country') == 'Netherlands' ? 'selected' : '' }}>Netherlands (+31)</option>
                    <option value="New Caledonia" {{ old('country') == 'New Caledonia' ? 'selected' : '' }}>New Caledonia (+687)</option>
                    <option value="New Zealand" {{ old('country') == 'New Zealand' ? 'selected' : '' }}>New Zealand (+64)</option>
                    <option value="Nicaragua" {{ old('country') == 'Nicaragua' ? 'selected' : '' }}>Nicaragua (+505)</option>
                    <option value="Niger" {{ old('country') == 'Niger' ? 'selected' : '' }}>Niger (+227)</option>
                    <option value="Nigeria" {{ old('country') == 'Nigeria' ? 'selected' : '' }}>Nigeria (+234)</option>
                    <option value="Niue" {{ old('country') == 'Niue' ? 'selected' : '' }}>Niue (+683)</option>
                    <option value="Norfolk Islands" {{ old('country') == 'Norfolk Islands' ? 'selected' : '' }}>Norfolk Islands (+672)</option>
                    <option value="Northern Marianas" {{ old('country') == 'Northern Marianas' ? 'selected' : '' }}>Northern Marianas (+670)</option>
                    <option value="Norway" {{ old('country') == 'Norway' ? 'selected' : '' }}>Norway (+47)</option>
                    <option value="Oman" {{ old('country') == 'Oman' ? 'selected' : '' }}>Oman (+968)</option>
                    <option value="Pakistan" {{ old('country') == 'Pakistan' ? 'selected' : '' }}>Pakistan (+92)</option>
                    <option value="Palau" {{ old('country') == 'Palau' ? 'selected' : '' }}>Palau (+680)</option>
                    <option value="Panama" {{ old('country') == 'Panama' ? 'selected' : '' }}>Panama (+507)</option>
                    <option value="Papua New Guinea" {{ old('country') == 'Papua New Guinea' ? 'selected' : '' }}>Papua New Guinea (+675)</option>
                    <option value="Paraguay" {{ old('country') == 'Paraguay' ? 'selected' : '' }}>Paraguay (+595)</option>
                    <option value="Peru" {{ old('country') == 'Peru' ? 'selected' : '' }}>Peru (+51)</option>
                    <option value="Philippines" {{ old('country') == 'Philippines' ? 'selected' : '' }}>Philippines (+63)</option>
                    <option value="Poland" {{ old('country') == 'Poland' ? 'selected' : '' }}>Poland (+48)</option>
                    <option value="Portugal" {{ old('country') == 'Portugal' ? 'selected' : '' }}>Portugal (+351)</option>
                    <option value="Puerto Rico" {{ old('country') == 'Puerto Rico' ? 'selected' : '' }}>Puerto Rico (+1787)</option>
                    <option value="Qatar" {{ old('country') == 'Qatar' ? 'selected' : '' }}>Qatar (+974)</option>
                    <option value="Reunion" {{ old('country') == 'Reunion' ? 'selected' : '' }}>Reunion (+262)</option>
                    <option value="Romania" {{ old('country') == 'Romania' ? 'selected' : '' }}>Romania (+40)</option>
                    <option value="Russia" {{ old('country') == 'Russia' ? 'selected' : '' }}>Russia (+7)</option>
                    <option value="Rwanda" {{ old('country') == 'Rwanda' ? 'selected' : '' }}>Rwanda (+250)</option>
                    <option value="San Marino" {{ old('country') == 'San Marino' ? 'selected' : '' }}>San Marino (+378)</option>
                    <option value="Sao Tome & Principe" {{ old('country') == 'Sao Tome & Principe' ? 'selected' : '' }}>Sao Tome & Principe (+239)</option>
                    <option value="Saudi Arabia" {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia (+966)</option>
                    <option value="Senegal" {{ old('country') == 'Senegal' ? 'selected' : '' }}>Senegal (+221)</option>
                    <option value="Serbia" {{ old('country') == 'Serbia' ? 'selected' : '' }}>Serbia (+381)</option>
                    <option value="Seychelles" {{ old('country') == 'Seychelles' ? 'selected' : '' }}>Seychelles (+248)</option>
                    <option value="Sierra Leone" {{ old('country') == 'Sierra Leone' ? 'selected' : '' }}>Sierra Leone (+232)</option>
                    <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore (+65)</option>
                    <option value="Slovak Republic" {{ old('country') == 'Slovak Republic' ? 'selected' : '' }}>Slovak Republic (+421)</option>
                    <option value="Slovenia" {{ old('country') == 'Slovenia' ? 'selected' : '' }}>Slovenia (+386)</option>
                    <option value="Solomon Islands" {{ old('country') == 'Solomon Islands' ? 'selected' : '' }}>Solomon Islands (+677)</option>
                    <option value="Somalia" {{ old('country') == 'Somalia' ? 'selected' : '' }}>Somalia (+252)</option>
                    <option value="South Africa" {{ old('country') == 'South Africa' ? 'selected' : '' }}>South Africa (+27)</option>
                    <option value="Spain" {{ old('country') == 'Spain' ? 'selected' : '' }}>Spain (+34)</option>
                    <option value="Sri Lanka" {{ old('country') == 'Sri Lanka' ? 'selected' : '' }}>Sri Lanka (+94)</option>
                    <option value="St. Helena" {{ old('country') == 'St. Helena' ? 'selected' : '' }}>St. Helena (+290)</option>
                    <option value="St. Kitts" {{ old('country') == 'St. Kitts' ? 'selected' : '' }}>St. Kitts (+1869)</option>
                    <option value="St. Lucia" {{ old('country') == 'St. Lucia' ? 'selected' : '' }}>St. Lucia (+1758)</option>
                    <option value="Sudan" {{ old('country') == 'Sudan' ? 'selected' : '' }}>Sudan (+249)</option>
                    <option value="Suriname" {{ old('country') == 'Suriname' ? 'selected' : '' }}>Suriname (+597)</option>
                    <option value="Swaziland" {{ old('country') == 'Swaziland' ? 'selected' : '' }}>Swaziland (+268)</option>
                    <option value="Sweden" {{ old('country') == 'Sweden' ? 'selected' : '' }}>Sweden (+46)</option>
                    <option value="Switzerland" {{ old('country') == 'Switzerland' ? 'selected' : '' }}>Switzerland (+41)</option>
                    <option value="Syria" {{ old('country') == 'Syria' ? 'selected' : '' }}>Syria (+963)</option>
                    <option value="Taiwan" {{ old('country') == 'Taiwan' ? 'selected' : '' }}>Taiwan (+886)</option>
                    <option value="Tajikistan" {{ old('country') == 'Tajikistan' ? 'selected' : '' }}>Tajikistan (+992)</option>
                    <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand (+66)</option>
                    <option value="Togo" {{ old('country') == 'Togo' ? 'selected' : '' }}>Togo (+228)</option>
                    <option value="Tonga" {{ old('country') == 'Tonga' ? 'selected' : '' }}>Tonga (+676)</option>
                    <option value="Trinidad & Tobago" {{ old('country') == 'Trinidad & Tobago' ? 'selected' : '' }}>Trinidad & Tobago (+1868)</option>
                    <option value="Tunisia" {{ old('country') == 'Tunisia' ? 'selected' : '' }}>Tunisia (+216)</option>
                    <option value="Turkey" {{ old('country') == 'Turkey' ? 'selected' : '' }}>Turkey (+90)</option>
                    <option value="Turkmenistan" {{ old('country') == 'Turkmenistan' ? 'selected' : '' }}>Turkmenistan (+993)</option>
                    <option value="Turks & Caicos Islands" {{ old('country') == 'Turks & Caicos Islands' ? 'selected' : '' }}>Turks & Caicos Islands (+1649)</option>
                    <option value="Tuvalu" {{ old('country') == 'Tuvalu' ? 'selected' : '' }}>Tuvalu (+688)</option>
                    <option value="Uganda" {{ old('country') == 'Uganda' ? 'selected' : '' }}>Uganda (+256)</option>
                    <option value="Ukraine" {{ old('country') == 'Ukraine' ? 'selected' : '' }}>Ukraine (+380)</option>
                    <option value="United Arab Emirates" {{ old('country') == 'United Arab Emirates' ? 'selected' : '' }}>United Arab Emirates (+971)</option>
                    <option value="Uruguay" {{ old('country') == 'Uruguay' ? 'selected' : '' }}>Uruguay (+598)</option>
                    <option value="Uzbekistan" {{ old('country') == 'Uzbekistan' ? 'selected' : '' }}>Uzbekistan (+998)</option>
                    <option value="Vanuatu" {{ old('country') == 'Vanuatu' ? 'selected' : '' }}>Vanuatu (+678)</option>
                    <option value="Vatican City" {{ old('country') == 'Vatican City' ? 'selected' : '' }}>Vatican City (+379)</option>
                    <option value="Venezuela" {{ old('country') == 'Venezuela' ? 'selected' : '' }}>Venezuela (+58)</option>
                    <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>Vietnam (+84)</option>
                    <option value="Virgin Islands - British" {{ old('country') == 'Virgin Islands - British' ? 'selected' : '' }}>Virgin Islands - British (+1)</option>
                    <option value="Virgin Islands - US" {{ old('country') == 'Virgin Islands - US' ? 'selected' : '' }}>Virgin Islands - US (+1)</option>
                    <option value="Wallis & Futuna" {{ old('country') == 'Wallis & Futuna' ? 'selected' : '' }}>Wallis & Futuna (+681)</option>
                    <option value="Yemen (North)" {{ old('country') == 'Yemen (North)' ? 'selected' : '' }}>Yemen (North)(+969)</option>
                    <option value="Yemen (South)" {{ old('country') == 'Yemen (South)' ? 'selected' : '' }}>Yemen (South)(+967)</option>
                    <option value="Zambia" {{ old('country') == 'Zambia' ? 'selected' : '' }}>Zambia (+260)</option>
                    <option value="Zimbabwe" {{ old('country') == 'Zimbabwe' ? 'selected' : '' }}>Zimbabwe (+263)</option>
                    </select>
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>
            </div>

            <!-- Phone -->
            <div class="flex items-start gap-4">
                <label for="phone" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Phone<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="phone" class="block w-full" type="text" name="phone" :value="old('phone')" required />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>
            </div>

            <!-- Address -->
            <div class="flex items-start gap-4">
                <label for="address1" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Address<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="address1" class="block w-full" type="text" name="address1" placeholder="Location" :value="old('address1')" required />
                    <x-input-error :messages="$errors->get('address1')" class="mt-2" />
                </div>
            </div>

            <!-- Hidden fields for address details (from Google Places API) -->
            <input type="hidden" id="address_lat" name="address_lat" value="">
            <input type="hidden" id="address_lng" name="address_lng" value="">
            <input type="hidden" id="address_formatted_address" name="address_formatted_address" value="">
            <input type="hidden" id="address_state" name="address_state" value="">
            <input type="hidden" id="address_city" name="address_city" value="">
            <input type="hidden" id="address_zipcode" name="address_zipcode" value="">

            <!-- Accept Terms -->
            <div class="flex items-start gap-4">
                <div class="w-1/4">
                </div>
                <div class="block">
                    <label for="accept_terms" class="inline-flex items-center">
                        <input id="accept_terms" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="accept_terms" value="1" required>
                        <span class="ms-2 text-sm text-gray-600">
                            <a href="#" class="underline">Accept Terms & Condition</a>
                            <a href="#" class="ml-2 underline">Help</a>
                        </span>
                    </label>
                    <x-input-error :messages="$errors->get('accept_terms')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-center mt-6">
                <x-primary-button>
                    {{ __('Save & Next') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

