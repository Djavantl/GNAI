import type { BootstrapSelectDemo, FileStyleDemo, SelectOption, SelectOptionGroup, SwitcheryItem, TouchSpinInputConfig } from './FormAdvanced.types';

export const tagCities: SelectOption[] = [
  { label: 'Amsterdam', value: 'Amsterdam' },
  { label: 'Washington', value: 'Washington' },
  { label: 'Sydney', value: 'Sydney' }
];

export const switcheryBasicItems: SwitcheryItem[] = [
  { color: '#f05050', checked: true },
  { color: '#81c868', checked: true },
  { color: '#ffbd4a', checked: true },
  { color: '#5d9cec', checked: true },
  { color: '#34d3eb', checked: true },
  { color: '#7266ba', checked: true },
  { color: '#fb6d9d', checked: true },
  { color: '#4c5667', checked: true },
  { color: '#98a6ad', checked: true }
];

export const switcherySizeItems: SwitcheryItem[] = [
  { color: '#f05050', size: 'small', checked: true },
  { color: '#81c868', checked: true },
  { color: '#ffbd4a', size: 'large', checked: true }
];

export const switcherySecondaryItems: SwitcheryItem[] = [
  { color: '#1AB394', secondaryColor: '#ED5565' },
  { color: '#fb6d9d', secondaryColor: '#7266ba' }
];

export const teamOptions: SelectOption[] = [
  {
    "value": "Dallas Cowboys",
    "label": "Dallas Cowboys"
  },
  {
    "value": "New York Giants",
    "label": "New York Giants"
  },
  {
    "value": "Philadelphia Eagles",
    "label": "Philadelphia Eagles"
  },
  {
    "value": "Washington Redskins",
    "label": "Washington Redskins"
  },
  {
    "value": "Chicago Bears",
    "label": "Chicago Bears"
  },
  {
    "value": "Detroit Lions",
    "label": "Detroit Lions"
  },
  {
    "value": "Green Bay Packers",
    "label": "Green Bay Packers"
  },
  {
    "value": "Minnesota Vikings",
    "label": "Minnesota Vikings"
  },
  {
    "value": "Atlanta Falcons",
    "label": "Atlanta Falcons"
  },
  {
    "value": "Carolina Panthers",
    "label": "Carolina Panthers"
  },
  {
    "value": "New Orleans Saints",
    "label": "New Orleans Saints"
  },
  {
    "value": "Tampa Bay Buccaneers",
    "label": "Tampa Bay Buccaneers"
  },
  {
    "value": "Arizona Cardinals",
    "label": "Arizona Cardinals"
  },
  {
    "value": "St. Louis Rams",
    "label": "St. Louis Rams"
  },
  {
    "value": "San Francisco 49ers",
    "label": "San Francisco 49ers"
  },
  {
    "value": "Seattle Seahawks",
    "label": "Seattle Seahawks"
  }
];

export const groupedTeamOptions: SelectOptionGroup[] = [
  {
    "label": "NFC EAST",
    "options": [
      {
        "value": "Dallas Cowboys",
        "label": "Dallas Cowboys"
      },
      {
        "value": "New York Giants",
        "label": "New York Giants"
      },
      {
        "value": "Philadelphia Eagles",
        "label": "Philadelphia Eagles"
      },
      {
        "value": "Washington Redskins",
        "label": "Washington Redskins"
      }
    ]
  },
  {
    "label": "NFC NORTH",
    "options": [
      {
        "value": "Chicago Bears",
        "label": "Chicago Bears"
      },
      {
        "value": "Detroit Lions",
        "label": "Detroit Lions"
      },
      {
        "value": "Green Bay Packers",
        "label": "Green Bay Packers"
      },
      {
        "value": "Minnesota Vikings",
        "label": "Minnesota Vikings"
      }
    ]
  },
  {
    "label": "NFC SOUTH",
    "options": [
      {
        "value": "Atlanta Falcons",
        "label": "Atlanta Falcons"
      },
      {
        "value": "Carolina Panthers",
        "label": "Carolina Panthers"
      },
      {
        "value": "New Orleans Saints",
        "label": "New Orleans Saints"
      },
      {
        "value": "Tampa Bay Buccaneers",
        "label": "Tampa Bay Buccaneers"
      }
    ]
  },
  {
    "label": "NFC WEST",
    "options": [
      {
        "value": "Arizona Cardinals",
        "label": "Arizona Cardinals"
      },
      {
        "value": "St. Louis Rams",
        "label": "St. Louis Rams"
      },
      {
        "value": "San Francisco 49ers",
        "label": "San Francisco 49ers"
      },
      {
        "value": "Seattle Seahawks",
        "label": "Seattle Seahawks"
      }
    ]
  }
];

export const countryOptions: SelectOption[] = [
  {
    "value": "AF",
    "label": "Afghanistan"
  },
  {
    "value": "AL",
    "label": "Albania"
  },
  {
    "value": "DZ",
    "label": "Algeria"
  },
  {
    "value": "AS",
    "label": "American Samoa"
  },
  {
    "value": "AD",
    "label": "Andorra"
  },
  {
    "value": "AO",
    "label": "Angola"
  },
  {
    "value": "AI",
    "label": "Anguilla"
  },
  {
    "value": "AQ",
    "label": "Antarctica"
  },
  {
    "value": "AR",
    "label": "Argentina"
  },
  {
    "value": "AM",
    "label": "Armenia"
  },
  {
    "value": "AW",
    "label": "Aruba"
  },
  {
    "value": "AU",
    "label": "Australia"
  },
  {
    "value": "AT",
    "label": "Austria"
  },
  {
    "value": "AZ",
    "label": "Azerbaijan"
  },
  {
    "value": "BS",
    "label": "Bahamas"
  },
  {
    "value": "BH",
    "label": "Bahrain"
  },
  {
    "value": "BD",
    "label": "Bangladesh"
  },
  {
    "value": "BB",
    "label": "Barbados"
  },
  {
    "value": "BY",
    "label": "Belarus"
  },
  {
    "value": "BE",
    "label": "Belgium"
  },
  {
    "value": "BZ",
    "label": "Belize"
  },
  {
    "value": "BJ",
    "label": "Benin"
  },
  {
    "value": "BM",
    "label": "Bermuda"
  },
  {
    "value": "BT",
    "label": "Bhutan"
  },
  {
    "value": "BO",
    "label": "Bolivia"
  },
  {
    "value": "BA",
    "label": "Bosnia and Herzegowina"
  },
  {
    "value": "BW",
    "label": "Botswana"
  },
  {
    "value": "BV",
    "label": "Bouvet Island"
  },
  {
    "value": "BR",
    "label": "Brazil"
  },
  {
    "value": "IO",
    "label": "British Indian Ocean Territory"
  },
  {
    "value": "BN",
    "label": "Brunei Darussalam"
  },
  {
    "value": "BG",
    "label": "Bulgaria"
  },
  {
    "value": "BF",
    "label": "Burkina Faso"
  },
  {
    "value": "BI",
    "label": "Burundi"
  },
  {
    "value": "KH",
    "label": "Cambodia"
  },
  {
    "value": "CM",
    "label": "Cameroon"
  },
  {
    "value": "CA",
    "label": "Canada"
  },
  {
    "value": "CV",
    "label": "Cape Verde"
  },
  {
    "value": "KY",
    "label": "Cayman Islands"
  },
  {
    "value": "CF",
    "label": "Central African Republic"
  },
  {
    "value": "TD",
    "label": "Chad"
  },
  {
    "value": "CL",
    "label": "Chile"
  },
  {
    "value": "CN",
    "label": "China"
  },
  {
    "value": "CX",
    "label": "Christmas Island"
  },
  {
    "value": "CC",
    "label": "Cocos (Keeling) Islands"
  },
  {
    "value": "CO",
    "label": "Colombia"
  },
  {
    "value": "KM",
    "label": "Comoros"
  },
  {
    "value": "CG",
    "label": "Congo"
  },
  {
    "value": "CD",
    "label": "Congo, the Democratic Republic of the"
  },
  {
    "value": "CK",
    "label": "Cook Islands"
  },
  {
    "value": "CR",
    "label": "Costa Rica"
  },
  {
    "value": "CI",
    "label": "Cote d'Ivoire"
  },
  {
    "value": "HR",
    "label": "Croatia (Hrvatska)"
  },
  {
    "value": "CU",
    "label": "Cuba"
  },
  {
    "value": "CY",
    "label": "Cyprus"
  },
  {
    "value": "CZ",
    "label": "Czech Republic"
  },
  {
    "value": "DK",
    "label": "Denmark"
  },
  {
    "value": "DJ",
    "label": "Djibouti"
  },
  {
    "value": "DM",
    "label": "Dominica"
  },
  {
    "value": "DO",
    "label": "Dominican Republic"
  },
  {
    "value": "EC",
    "label": "Ecuador"
  },
  {
    "value": "EG",
    "label": "Egypt"
  },
  {
    "value": "SV",
    "label": "El Salvador"
  },
  {
    "value": "GQ",
    "label": "Equatorial Guinea"
  },
  {
    "value": "ER",
    "label": "Eritrea"
  },
  {
    "value": "EE",
    "label": "Estonia"
  },
  {
    "value": "ET",
    "label": "Ethiopia"
  },
  {
    "value": "FK",
    "label": "Falkland Islands (Malvinas)"
  },
  {
    "value": "FO",
    "label": "Faroe Islands"
  },
  {
    "value": "FJ",
    "label": "Fiji"
  },
  {
    "value": "FI",
    "label": "Finland"
  },
  {
    "value": "FR",
    "label": "France"
  },
  {
    "value": "GF",
    "label": "French Guiana"
  },
  {
    "value": "PF",
    "label": "French Polynesia"
  },
  {
    "value": "TF",
    "label": "French Southern Territories"
  },
  {
    "value": "GA",
    "label": "Gabon"
  },
  {
    "value": "GM",
    "label": "Gambia"
  },
  {
    "value": "GE",
    "label": "Georgia"
  },
  {
    "value": "DE",
    "label": "Germany"
  },
  {
    "value": "GH",
    "label": "Ghana"
  },
  {
    "value": "GI",
    "label": "Gibraltar"
  },
  {
    "value": "GR",
    "label": "Greece"
  },
  {
    "value": "GL",
    "label": "Greenland"
  },
  {
    "value": "GD",
    "label": "Grenada"
  },
  {
    "value": "GP",
    "label": "Guadeloupe"
  },
  {
    "value": "GU",
    "label": "Guam"
  },
  {
    "value": "GT",
    "label": "Guatemala"
  },
  {
    "value": "GN",
    "label": "Guinea"
  },
  {
    "value": "GW",
    "label": "Guinea-Bissau"
  },
  {
    "value": "GY",
    "label": "Guyana"
  },
  {
    "value": "HT",
    "label": "Haiti"
  },
  {
    "value": "HM",
    "label": "Heard and Mc Donald Islands"
  },
  {
    "value": "VA",
    "label": "Holy See (Vatican City State)"
  },
  {
    "value": "HN",
    "label": "Honduras"
  },
  {
    "value": "HK",
    "label": "Hong Kong"
  },
  {
    "value": "HU",
    "label": "Hungary"
  },
  {
    "value": "IS",
    "label": "Iceland"
  },
  {
    "value": "IN",
    "label": "India"
  },
  {
    "value": "ID",
    "label": "Indonesia"
  },
  {
    "value": "IR",
    "label": "Iran (Islamic Republic of)"
  },
  {
    "value": "IQ",
    "label": "Iraq"
  },
  {
    "value": "IE",
    "label": "Ireland"
  },
  {
    "value": "IL",
    "label": "Israel"
  },
  {
    "value": "IT",
    "label": "Italy"
  },
  {
    "value": "JM",
    "label": "Jamaica"
  },
  {
    "value": "JP",
    "label": "Japan"
  },
  {
    "value": "JO",
    "label": "Jordan"
  },
  {
    "value": "KZ",
    "label": "Kazakhstan"
  },
  {
    "value": "KE",
    "label": "Kenya"
  },
  {
    "value": "KI",
    "label": "Kiribati"
  },
  {
    "value": "KP",
    "label": "Korea, Democratic People's Republic of"
  },
  {
    "value": "KR",
    "label": "Korea, Republic of"
  },
  {
    "value": "KW",
    "label": "Kuwait"
  },
  {
    "value": "KG",
    "label": "Kyrgyzstan"
  },
  {
    "value": "LA",
    "label": "Lao People's Democratic Republic"
  },
  {
    "value": "LV",
    "label": "Latvia"
  },
  {
    "value": "LB",
    "label": "Lebanon"
  },
  {
    "value": "LS",
    "label": "Lesotho"
  },
  {
    "value": "LR",
    "label": "Liberia"
  },
  {
    "value": "LY",
    "label": "Libyan Arab Jamahiriya"
  },
  {
    "value": "LI",
    "label": "Liechtenstein"
  },
  {
    "value": "LT",
    "label": "Lithuania"
  },
  {
    "value": "LU",
    "label": "Luxembourg"
  },
  {
    "value": "MO",
    "label": "Macau"
  },
  {
    "value": "MK",
    "label": "Macedonia, The Former Yugoslav Republic of"
  },
  {
    "value": "MG",
    "label": "Madagascar"
  },
  {
    "value": "MW",
    "label": "Malawi"
  },
  {
    "value": "MY",
    "label": "Malaysia"
  },
  {
    "value": "MV",
    "label": "Maldives"
  },
  {
    "value": "ML",
    "label": "Mali"
  },
  {
    "value": "MT",
    "label": "Malta"
  },
  {
    "value": "MH",
    "label": "Marshall Islands"
  },
  {
    "value": "MQ",
    "label": "Martinique"
  },
  {
    "value": "MR",
    "label": "Mauritania"
  },
  {
    "value": "MU",
    "label": "Mauritius"
  },
  {
    "value": "YT",
    "label": "Mayotte"
  },
  {
    "value": "MX",
    "label": "Mexico"
  },
  {
    "value": "FM",
    "label": "Micronesia, Federated States of"
  },
  {
    "value": "MD",
    "label": "Moldova, Republic of"
  },
  {
    "value": "MC",
    "label": "Monaco"
  },
  {
    "value": "MN",
    "label": "Mongolia"
  },
  {
    "value": "MS",
    "label": "Montserrat"
  },
  {
    "value": "MA",
    "label": "Morocco"
  },
  {
    "value": "MZ",
    "label": "Mozambique"
  },
  {
    "value": "MM",
    "label": "Myanmar"
  },
  {
    "value": "NA",
    "label": "Namibia"
  },
  {
    "value": "NR",
    "label": "Nauru"
  },
  {
    "value": "NP",
    "label": "Nepal"
  },
  {
    "value": "NL",
    "label": "Netherlands"
  },
  {
    "value": "AN",
    "label": "Netherlands Antilles"
  },
  {
    "value": "NC",
    "label": "New Caledonia"
  },
  {
    "value": "NZ",
    "label": "New Zealand"
  },
  {
    "value": "NI",
    "label": "Nicaragua"
  },
  {
    "value": "NE",
    "label": "Niger"
  },
  {
    "value": "NG",
    "label": "Nigeria"
  },
  {
    "value": "NU",
    "label": "Niue"
  },
  {
    "value": "NF",
    "label": "Norfolk Island"
  },
  {
    "value": "MP",
    "label": "Northern Mariana Islands"
  },
  {
    "value": "NO",
    "label": "Norway"
  },
  {
    "value": "OM",
    "label": "Oman"
  },
  {
    "value": "PK",
    "label": "Pakistan"
  },
  {
    "value": "PW",
    "label": "Palau"
  },
  {
    "value": "PA",
    "label": "Panama"
  },
  {
    "value": "PG",
    "label": "Papua New Guinea"
  },
  {
    "value": "PY",
    "label": "Paraguay"
  },
  {
    "value": "PE",
    "label": "Peru"
  },
  {
    "value": "PH",
    "label": "Philippines"
  },
  {
    "value": "PN",
    "label": "Pitcairn"
  },
  {
    "value": "PL",
    "label": "Poland"
  },
  {
    "value": "PT",
    "label": "Portugal"
  },
  {
    "value": "PR",
    "label": "Puerto Rico"
  },
  {
    "value": "QA",
    "label": "Qatar"
  },
  {
    "value": "RE",
    "label": "Reunion"
  },
  {
    "value": "RO",
    "label": "Romania"
  },
  {
    "value": "RU",
    "label": "Russian Federation"
  },
  {
    "value": "RW",
    "label": "Rwanda"
  },
  {
    "value": "KN",
    "label": "Saint Kitts and Nevis"
  },
  {
    "value": "LC",
    "label": "Saint LUCIA"
  },
  {
    "value": "VC",
    "label": "Saint Vincent and the Grenadines"
  },
  {
    "value": "WS",
    "label": "Samoa"
  },
  {
    "value": "SM",
    "label": "San Marino"
  },
  {
    "value": "ST",
    "label": "Sao Tome and Principe"
  },
  {
    "value": "SA",
    "label": "Saudi Arabia"
  },
  {
    "value": "SN",
    "label": "Senegal"
  },
  {
    "value": "SC",
    "label": "Seychelles"
  },
  {
    "value": "SL",
    "label": "Sierra Leone"
  },
  {
    "value": "SG",
    "label": "Singapore"
  },
  {
    "value": "SK",
    "label": "Slovakia (Slovak Republic)"
  },
  {
    "value": "SI",
    "label": "Slovenia"
  },
  {
    "value": "SB",
    "label": "Solomon Islands"
  },
  {
    "value": "SO",
    "label": "Somalia"
  },
  {
    "value": "ZA",
    "label": "South Africa"
  },
  {
    "value": "GS",
    "label": "South Georgia and the South Sandwich Islands"
  },
  {
    "value": "ES",
    "label": "Spain"
  },
  {
    "value": "LK",
    "label": "Sri Lanka"
  },
  {
    "value": "SH",
    "label": "St. Helena"
  },
  {
    "value": "PM",
    "label": "St. Pierre and Miquelon"
  },
  {
    "value": "SD",
    "label": "Sudan"
  },
  {
    "value": "SR",
    "label": "Suriname"
  },
  {
    "value": "SJ",
    "label": "Svalbard and Jan Mayen Islands"
  },
  {
    "value": "SZ",
    "label": "Swaziland"
  },
  {
    "value": "SE",
    "label": "Sweden"
  },
  {
    "value": "CH",
    "label": "Switzerland"
  },
  {
    "value": "SY",
    "label": "Syrian Arab Republic"
  },
  {
    "value": "TW",
    "label": "Taiwan, Province of China"
  },
  {
    "value": "TJ",
    "label": "Tajikistan"
  },
  {
    "value": "TZ",
    "label": "Tanzania, United Republic of"
  },
  {
    "value": "TH",
    "label": "Thailand"
  },
  {
    "value": "TG",
    "label": "Togo"
  },
  {
    "value": "TK",
    "label": "Tokelau"
  },
  {
    "value": "TO",
    "label": "Tonga"
  },
  {
    "value": "TT",
    "label": "Trinidad and Tobago"
  },
  {
    "value": "TN",
    "label": "Tunisia"
  },
  {
    "value": "TR",
    "label": "Turkey"
  },
  {
    "value": "TM",
    "label": "Turkmenistan"
  },
  {
    "value": "TC",
    "label": "Turks and Caicos Islands"
  },
  {
    "value": "TV",
    "label": "Tuvalu"
  },
  {
    "value": "UG",
    "label": "Uganda"
  },
  {
    "value": "UA",
    "label": "Ukraine"
  },
  {
    "value": "AE",
    "label": "United Arab Emirates"
  },
  {
    "value": "GB",
    "label": "United Kingdom"
  },
  {
    "value": "US",
    "label": "United States"
  },
  {
    "value": "UM",
    "label": "United States Minor Outlying Islands"
  },
  {
    "value": "UY",
    "label": "Uruguay"
  },
  {
    "value": "UZ",
    "label": "Uzbekistan"
  },
  {
    "value": "VU",
    "label": "Vanuatu"
  },
  {
    "value": "VE",
    "label": "Venezuela"
  },
  {
    "value": "VN",
    "label": "Viet Nam"
  },
  {
    "value": "VG",
    "label": "Virgin Islands (British)"
  },
  {
    "value": "VI",
    "label": "Virgin Islands (U.S.)"
  },
  {
    "value": "WF",
    "label": "Wallis and Futuna Islands"
  },
  {
    "value": "EH",
    "label": "Western Sahara"
  },
  {
    "value": "YE",
    "label": "Yemen"
  },
  {
    "value": "ZM",
    "label": "Zambia"
  },
  {
    "value": "ZW",
    "label": "Zimbabwe"
  }
];

export const timeZoneGroups: SelectOptionGroup[] = [
  {
    "label": "Alaskan/Hawaiian Time Zone",
    "options": [
      {
        "value": "AK",
        "label": "Alaska"
      },
      {
        "value": "HI",
        "label": "Hawaii"
      }
    ]
  },
  {
    "label": "Pacific Time Zone",
    "options": [
      {
        "value": "CA",
        "label": "California"
      },
      {
        "value": "NV",
        "label": "Nevada"
      },
      {
        "value": "OR",
        "label": "Oregon"
      },
      {
        "value": "WA",
        "label": "Washington"
      }
    ]
  },
  {
    "label": "Mountain Time Zone",
    "options": [
      {
        "value": "AZ",
        "label": "Arizona"
      },
      {
        "value": "CO",
        "label": "Colorado"
      },
      {
        "value": "ID",
        "label": "Idaho"
      },
      {
        "value": "MT",
        "label": "Montana"
      },
      {
        "value": "NE",
        "label": "Nebraska"
      },
      {
        "value": "NM",
        "label": "New Mexico"
      },
      {
        "value": "ND",
        "label": "North Dakota"
      },
      {
        "value": "UT",
        "label": "Utah"
      },
      {
        "value": "WY",
        "label": "Wyoming"
      }
    ]
  },
  {
    "label": "Central Time Zone",
    "options": [
      {
        "value": "AL",
        "label": "Alabama"
      },
      {
        "value": "AR",
        "label": "Arkansas"
      },
      {
        "value": "IL",
        "label": "Illinois"
      },
      {
        "value": "IA",
        "label": "Iowa"
      },
      {
        "value": "KS",
        "label": "Kansas"
      },
      {
        "value": "KY",
        "label": "Kentucky"
      },
      {
        "value": "LA",
        "label": "Louisiana"
      },
      {
        "value": "MN",
        "label": "Minnesota"
      },
      {
        "value": "MS",
        "label": "Mississippi"
      },
      {
        "value": "MO",
        "label": "Missouri"
      },
      {
        "value": "OK",
        "label": "Oklahoma"
      },
      {
        "value": "SD",
        "label": "South Dakota"
      },
      {
        "value": "TX",
        "label": "Texas"
      },
      {
        "value": "TN",
        "label": "Tennessee"
      },
      {
        "value": "WI",
        "label": "Wisconsin"
      }
    ]
  },
  {
    "label": "Eastern Time Zone",
    "options": [
      {
        "value": "CT",
        "label": "Connecticut"
      },
      {
        "value": "DE",
        "label": "Delaware"
      },
      {
        "value": "FL",
        "label": "Florida"
      },
      {
        "value": "GA",
        "label": "Georgia"
      },
      {
        "value": "IN",
        "label": "Indiana"
      },
      {
        "value": "ME",
        "label": "Maine"
      },
      {
        "value": "MD",
        "label": "Maryland"
      },
      {
        "value": "MA",
        "label": "Massachusetts"
      },
      {
        "value": "MI",
        "label": "Michigan"
      },
      {
        "value": "NH",
        "label": "New Hampshire"
      },
      {
        "value": "NJ",
        "label": "New Jersey"
      },
      {
        "value": "NY",
        "label": "New York"
      },
      {
        "value": "NC",
        "label": "North Carolina"
      },
      {
        "value": "OH",
        "label": "Ohio"
      },
      {
        "value": "PA",
        "label": "Pennsylvania"
      },
      {
        "value": "RI",
        "label": "Rhode Island"
      },
      {
        "value": "SC",
        "label": "South Carolina"
      },
      {
        "value": "VT",
        "label": "Vermont"
      },
      {
        "value": "VA",
        "label": "Virginia"
      },
      {
        "value": "WV",
        "label": "West Virginia"
      }
    ]
  }
];

export const bootstrapSelectOptions: SelectOption[] = [
  { label: 'Mustard', value: 'Mustard' },
  { label: 'Ketchup', value: 'Ketchup' },
  { label: 'Relish', value: 'Relish' }
];

export const bootstrapSelectGroups: BootstrapSelectDemo['optgroups'] = [
  { label: 'Picnic', options: bootstrapSelectOptions },
  {
    label: 'Camping',
    options: [
      { label: 'Tent', value: 'Tent' },
      { label: 'Flashlight', value: 'Flashlight' },
      { label: 'Toilet Paper', value: 'Toilet Paper' }
    ]
  }
];

export const bootstrapSelectColorDemos: BootstrapSelectDemo[] = [
  { dataStyle: 'btn-default btn-custom', options: bootstrapSelectOptions },
  { dataStyle: 'btn-primary btn-custom', options: bootstrapSelectOptions },
  { dataStyle: 'btn-success btn-custom', options: bootstrapSelectOptions },
  { dataStyle: 'btn-warning btn-custom', options: bootstrapSelectOptions },
  { dataStyle: 'btn-danger btn-custom', options: bootstrapSelectOptions },
  { dataStyle: 'btn-purple btn-custom', options: bootstrapSelectOptions }
];

export const bootstrapSelectIconDemo: BootstrapSelectDemo = {
  dataStyle: 'btn-white',
  options: [
    { label: 'Mustard', value: 'Mustard', icon: 'glyphicon-glass' },
    { label: 'Ketchup', value: 'Ketchup', icon: 'glyphicon-heart' },
    { label: 'Relish', value: 'Relish', icon: 'glyphicon-film' },
    { label: 'Mayonnaise', value: 'Mayonnaise', icon: 'glyphicon-home' },
    { label: 'Barbecue Sauce', value: 'Barbecue Sauce', icon: 'glyphicon-print' }
  ]
};

export const bootstrapSelectSearchOptions: SelectOption[] = [
  { label: 'Hot Dog, Fries and a Soda', value: 'Hot Dog, Fries and a Soda' },
  { label: 'Burger, Shake and a Smile', value: 'Burger, Shake and a Smile' },
  { label: 'Sugar, Spice and all things nice', value: 'Sugar, Spice and all things nice' }
];

export const bootstrapSelectTitledOptions: BootstrapSelectDemo['options'] = [
  { label: 'Hot Dog, Fries and a Soda', value: 'Hot Dog, Fries and a Soda', title: 'Combo 1' },
  { label: 'Burger, Shake and a Smile', value: 'Burger, Shake and a Smile', title: 'Combo 2' },
  { label: 'Sugar, Spice and all things nice', value: 'Sugar, Spice and all things nice', title: 'Combo 3' }
];

export const bootstrapSelectCountOptions: SelectOption[] = [
  ...bootstrapSelectOptions,
  { label: 'Onions', value: 'Onions' }
];

export const fileStyleLeftDemos: FileStyleDemo[] = [
  { label: 'Input de arquivo padrão', attributes: { 'data-buttonname': 'btn-white' } },
  { label: 'File style sem input', attributes: { 'data-input': false } },
  { label: 'File style sem ícone', attributes: { 'data-icon': false, 'data-buttonname': 'btn-white' } },
  { label: 'File style com texto customizado', attributes: { 'data-buttontext': 'Selecionar arquivo', 'data-buttonname': 'btn-white' } },
  { label: 'File style com estilo de botão', attributes: { 'data-buttonname': 'btn-primary' } }
];

export const fileStyleRightDemos: FileStyleDemo[] = [
  { label: 'File style pequeno', attributes: { 'data-size': 'sm' } },
  { label: 'File style com ícone customizado', attributes: { 'data-iconname': 'fa fa-cloud-upload' } },
  { label: 'File style desabilitado', attributes: { 'data-disabled': true } },
  { label: 'File style antes do botão', attributes: { 'data-buttonbefore': true } },
  { label: 'Placeholder do file style', attributes: { 'data-placeholder': 'Nenhum arquivo' } }
];

export const touchSpinLeftInputs: TouchSpinInputConfig[] = [
  { label: 'Alinhamento vertical dos botões', name: 'vertical-spin', className: 'vertical-spin' },
  {
    id: 'demo0',
    label: 'Usando atributos data',
    name: 'demo0',
    value: '55',
    attributes: {
      'data-bts-min': 0,
      'data-bts-max': 100,
      'data-bts-init-val': '',
      'data-bts-step': 1,
      'data-bts-decimal': 0,
      'data-bts-step-interval': 100,
      'data-bts-force-step-divisibility': 'round',
      'data-bts-step-interval-delay': 500,
      'data-bts-prefix': '',
      'data-bts-postfix': '',
      'data-bts-prefix-extra-class': '',
      'data-bts-postfix-extra-class': '',
      'data-bts-booster': true,
      'data-bts-boostat': 10,
      'data-bts-max-boosted-step': false,
      'data-bts-mousewheel': true,
      'data-bts-button-down-class': 'btn btn-default',
      'data-bts-button-up-class': 'btn btn-default'
    }
  },
  { id: 'demo1', label: 'Exemplo com postfix grande', name: 'demo1', value: '55' },
  { id: 'demo2', label: 'Com prefixo', name: 'demo2', value: '0', className: 'form-control' }
];

export const touchSpinRightInputs: TouchSpinInputConfig[] = [
  { id: 'demo3', label: 'Inicializar com valor vazio:', name: 'demo3' },
  { id: 'demo3_21', label: 'Atributo value não definido (aplica settings.initval)', name: 'demo3_21' },
  { id: 'demo3_22', label: 'Valor definido explicitamente como 33 (ignora settings.initval)', name: 'demo3_22', value: '33' },
  { id: 'demo5', label: 'Grupo de botões', name: 'demo5', value: '50', className: 'form-control' }
];

