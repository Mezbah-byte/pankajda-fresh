<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CountriesSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('countries')->countAllResults() > 0) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $countries = [
            // Priority for Bangladeshi visa context
            ['Bangladesh',            'BD'],
            ['Saudi Arabia',          'SA'],
            ['United Arab Emirates',  'AE'],
            ['Qatar',                 'QA'],
            ['Kuwait',                'KW'],
            ['Bahrain',               'BH'],
            ['Oman',                  'OM'],
            ['Malaysia',              'MY'],
            ['Singapore',             'SG'],
            ['Jordan',                'JO'],
            ['Libya',                 'LY'],
            ['Maldives',              'MV'],
            // Rest of Asia-Pacific
            ['Afghanistan',           'AF'],
            ['Australia',             'AU'],
            ['Bhutan',                'BT'],
            ['Brunei',                'BN'],
            ['Cambodia',              'KH'],
            ['China',                 'CN'],
            ['Hong Kong',             'HK'],
            ['India',                 'IN'],
            ['Indonesia',             'ID'],
            ['Iran',                  'IR'],
            ['Iraq',                  'IQ'],
            ['Japan',                 'JP'],
            ['Kazakhstan',            'KZ'],
            ['Kyrgyzstan',            'KG'],
            ['Laos',                  'LA'],
            ['Lebanon',               'LB'],
            ['Mongolia',              'MN'],
            ['Myanmar',               'MM'],
            ['Nepal',                 'NP'],
            ['New Zealand',           'NZ'],
            ['North Korea',           'KP'],
            ['Pakistan',              'PK'],
            ['Palestine',             'PS'],
            ['Philippines',           'PH'],
            ['South Korea',           'KR'],
            ['Sri Lanka',             'LK'],
            ['Syria',                 'SY'],
            ['Taiwan',                'TW'],
            ['Tajikistan',            'TJ'],
            ['Thailand',              'TH'],
            ['Turkmenistan',          'TM'],
            ['Uzbekistan',            'UZ'],
            ['Vietnam',               'VN'],
            ['Yemen',                 'YE'],
            // Africa
            ['Algeria',               'DZ'],
            ['Angola',                'AO'],
            ['Egypt',                 'EG'],
            ['Ethiopia',              'ET'],
            ['Ghana',                 'GH'],
            ['Kenya',                 'KE'],
            ['Morocco',               'MA'],
            ['Nigeria',               'NG'],
            ['South Africa',          'ZA'],
            ['Sudan',                 'SD'],
            ['Tanzania',              'TZ'],
            ['Tunisia',               'TN'],
            ['Uganda',                'UG'],
            // Europe
            ['Austria',               'AT'],
            ['Belgium',               'BE'],
            ['Bulgaria',              'BG'],
            ['Croatia',               'HR'],
            ['Cyprus',                'CY'],
            ['Czech Republic',        'CZ'],
            ['Denmark',               'DK'],
            ['Finland',               'FI'],
            ['France',                'FR'],
            ['Germany',               'DE'],
            ['Greece',                'GR'],
            ['Hungary',               'HU'],
            ['Ireland',               'IE'],
            ['Italy',                 'IT'],
            ['Luxembourg',            'LU'],
            ['Malta',                 'MT'],
            ['Netherlands',           'NL'],
            ['Norway',                'NO'],
            ['Poland',                'PL'],
            ['Portugal',              'PT'],
            ['Romania',               'RO'],
            ['Russia',                'RU'],
            ['Slovakia',              'SK'],
            ['Slovenia',              'SI'],
            ['Spain',                 'ES'],
            ['Sweden',                'SE'],
            ['Switzerland',           'CH'],
            ['Turkey',                'TR'],
            ['Ukraine',               'UA'],
            ['United Kingdom',        'GB'],
            // Americas
            ['Argentina',             'AR'],
            ['Brazil',                'BR'],
            ['Canada',                'CA'],
            ['Chile',                 'CL'],
            ['Colombia',              'CO'],
            ['Cuba',                  'CU'],
            ['Mexico',                'MX'],
            ['Peru',                  'PE'],
            ['United States',         'US'],
            ['Venezuela',             'VE'],
            // Other
            ['Israel',                'IL'],
        ];

        foreach ($countries as $i => [$name, $iso]) {
            $this->db->table('countries')->insert([
                'un_id'      => generate_un_id('CTY'),
                'name'       => $name,
                'iso_code'   => $iso,
                'sort_order' => ($i + 1) * 10,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
