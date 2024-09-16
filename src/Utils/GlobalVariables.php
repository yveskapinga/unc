<?php

namespace App\Utils;

use GuzzleHttp\RetryMiddleware;

class GlobalVariables
{


    public static function getCategories_with_descriptions(){
        return self::$categories_with_descriptions;
    }

    public static function getLevels()
    {
        return self::$levels;
    }

    public static function getMembershipType()
    {
        return self::$membershipType;
    }

    public static function getFunctionsForLevel($level)
    {
        return self::$functions[$level] ?? [];
    }

    public static function getFonctionsForMembership(){
        return self::$fonctionsForMembership;
    }

    public static function getRoles(){
        return self::$roles;
    }

    // Les types de membres pour l'entité Membership
    private static $membershipType = [
            'Membre fondateur' => 'Membre fondateur',
            'Membre éffectif' => 'Membre éffectif',
            'Membre d\'honneur' => 'Membre d\'honneur',
            'Membre sympathisant' => 'Membre sympathisant',
    ];
    private static $roles =
    [
        'Super admin' => 'ROLE_SUPER_ADMIN',
        'Admin' => 'ROLE_ADMIN',
        'Modérateur' => 'ROLE_MODERATOR',
        'User' => 'ROLE_USER',

        // Ajoutez d'autres rôles si nécessaire
    ];

    public static $users = [
        ["SAMBA LEON HERITIER", "Vice-Président en charge de la diplomatie et affaire politique"],
        ["KASANG MAWIT Laeticia", "Vice-présidente en charge des relations avec les partis politiques, suivi des alliances politiques et porte-parole"],
        ["KAFUNDA NGOY GACIEN", "Vice-président en charge d' études et stratégies"],
        ["TSHIBANGU KATSHUNGA Axel", "Vice-Président en charge de l' emploi et suivi des ressources financières"],
        ["ZOMBO WA ZOMBO Cédric", "Vice-président en charge de renformes, bonne gouvernance et lutte contre la corruption"],
        ["KASONGO NGOY Patrick", "Vice-président en charge du recrutement et implantation"],
        ["MBUYI BITANGILAYI Aaron", "Vice-président en charge de relations avec les mouvements associatifs"],
        ["KABAMBA JUSCAR", "Coordonnateur en charge de la mobilisation et propagande"],
        ["KAPADI NAWEJ JENOVIC", "Coordonnateur adjoint en charge de la mobilisation propagande"],
        ["KISHA KALONDA Mathieu", "Coordonnateur adjoint en charge de la mobilisation propagande"],
        ["MUKANYA NUMBI JEANNOT", "Coordonnateur adjoint en charge de la mobilisation propagande"],
        ["KASONGO MWANA Steeve", "Coordonnateur en charge de la cellule communication"],
        ["YAV KASEND Benatard", "Coordonnateur adjoint en charge de la cellule communication"],
        ["KITENGE LUCIEN", "Coordonnateur adjoint de la cellule de la communication"],
        ["KASONGO LUCIEN", "Coordonnateur adjoint de la cellule de communication"],
        ["AMBAY CHRISTIAN", "Coordonnateur en charge de l' organisation des manifestations"],
        ["BOMBO KOSA SAMUKAMBA Bob", "Coordonnateur en charge de l'organisation des manifestations"],
        ["MALU MASANKA FRANCINE", "Coordonnateur adjoint en charge de l'organisation des manifestations"],
        ["BANZA MATEMBO Michael", "Coordonnateur en charge de l' emploi et suivi"],
        ["MPANGA KAUMBA Blaise", "Coordonnateur adjoint en charge de l' emploi et suivi"],
        ["BABA KAUNZA Le jeune", "Coordonnateur adjoint en charge de l' emploi et suivi"],
        ["KAYEMBE SHISSO Mathurin", "Coordonnateur en charge des sports et loisirs"],
        ["GHANDY NZENG", "Coordonnateur adjoint en charge de sports et loisirs"],
        ["JONATHAN MUKUMBU", "Coordonnateur adjoint en charge de sports et loisirs"],
        ["MUMBA WA MUMBA Mélanie", "Coordonnatrice en charge des affaires sociales"],
        ["NGOIE KABELA ANNIE", "Coordonnatrice adjointe en charge des affaires sociales"],
        ["KARAND MWANGAL Justin", "Coordonnateur en charge de recrutement et implantation"],
        ["MIANDA MBELO Hono", "Coordonnateur adjoint en charge de recrutement et implantation"],
        ["YAV AMUYUK Mathieu", "Coordonnateur en charge des questions sécuritaires et relations publiques"],
        ["NGOIE MUTUALE Vanessa", "Coordonnatrice adjointe en charge des questions sécuritaires et relations publiques"],
        ["KIBINDA JEAN PIERRE ELIE", "Coordonnateur adjoint en charge des questions sécuritaires et relations publiques"],
        ["MALESO MWITAMBWILA Joseph", "Coordonnateur en charge de l'école du parti"],
        ["VICTOR AKILA Le Roy", "Coordonnateur adjoint en charge de l' école du parti"],
    ];
    public static $users2 = [
        ["DENDRY PASCAL Pakson", "Coordonnateur en charge de la santé"],
        ["MAJOND RUBEND Trésor", "Coordonnateur adjoint en charge de la santé"],
        ["MISENGA LENGE PRISCA", "Coordonnateur adjoint en charge de la santé"],
        ["MUMBA MUJINGA Chancelle", "Coordonnatrice en charge de protocole"],
        ["KALOMBO ZANGOLA Rémy", "Coordonnateur adjoint en charge de protocole"],
        ["PATRICIA NDALA", "Coordonnatrice adjointe en charge de protocole"],
        ["MIJKENGE ISAAC", "Coordonnateur en charge de questions juridiques"],
        ["STEEVE DIUR", "Coordonnateur adjoint en charge des questions juridiques"],
        ["BOYO FASAYA Freddy", "Coordonnateur en charge de la cellule d' animation politique"],
        ["KABONDO Ademard KAFINDO", "Coordonnateur adjoint en charge de la cellule d'animation politique"],
        ["MASENGO Celda INAMAJABA", "Coordonnatrice en charge de la mobilisation des ressources financières"],
        ["KIWELE KALUNGE JUSTINE", "Coordonnatrice adjointe de la mobilisation des ressources financières"],
        ["IBOYA MBALE Merveille", "Coordonnatrice en charge des membres d' honneurs"],
        ["MAKAY TSHELEKA Isaac", "Coordonnateur en charge de l'audit"],
        ["NGOY WA TUZUMBA Gauthier", "Coordonnateur adjoint en charge de l'audit"],
        ["KAYEMBE Samy", "Coordonnateur en charge des missions stratégiques"],
        ["MUSHIDI YAV SYLVA", "Coordonnateur adjoint en charge des missions stratégiques"],
        ["MUSHIDI YAV Diego", "Coordonnateur en charge de la visibilité"],
        ["NKAKU BITANGILAYI Moise", "Coordonnateur en charge de l'organisation et suivi électoral"],
        ["YEMB MUKAZ Franck", "Coordonnateur adjoint en charge de l'organisation et suivi électoral"],
        ["KAPENDA MUJINGA DJO", "Président fédéral des jeunes, ville de Kolwezi"],
        ["KANFWA KALUKUTA ELIE", "Président fédéral adjoint des jeunes chargé de la mobilisation et propagande"],
        ["RODRIGUEZ KABEYA Sarah", "Président fédéral adjoint chargé d' études et stratégies"],
        ["KISENGA MWEMA Jacques", "Président fédéral adjoint chargé de recrutement et suivi des organes de bases"],
        ["RODRIGUEZ KABEYA", "Président fédéral adjoint des jeunes chargé d'organisation et administration"],
        ["VUMBI MILEMBA Nicole", "Présidente inter fédération des femmes"],
        ["KUMWIMBA NUMBI Alice", "Première vice-présidente : chargée de recrutement, implantation et suivis de base"],
        ["KAYEMBE MASUDI Naomie", "Deuxième vice-présidente : chargée des affaires sociales"],
        ["LULUWA MWAMBA Véronique", "Troisième vice-présidente : chargée de relation avec les partis politiques et suivi des alliances politiques"],
        ["MANGWEJI CHALA Marlene", "Quatrième vice-présidente : chargée d'études et stratégie"],
        ["KABAMBA KABWE Carine", "Rapporteur"],
        ["KAJI MASHINGO Blandine", "Rapporteur adjoint"],
        ["PEMA OLANGI Judith", "Trésorière"],
        ["MUJINGA KANDEY Huguette", "Trésorière Adjointe"],
        ["ORNELIE KAFUKU", "Chef protocole"],
        ["DJILABU NYEMBWE Lyna", "Première protocole"],
        ["MASENGO INAMANJABA Selda", "Deuxième protocole"],
    ];
    public static $users3 = [
        ["KAFIA MWAMB Sylvie", "Implantation et organisation de base"],
        ["SAMBA KAJAM Jofrette", "Implantation et organisation de base adjointe"],
        ["MAND KACHAY Gotetti", "Mouvement associative"],
        ["KAWANG MUTUND Carine", "Mouvement associative adjointe"],
        ["KAMBAJ TSHIPENG Lydie", "Mobilisation"],
        ["MUSENG MWIZ Sabrina", "Mobilisation adjointe"],
        ["MWADI KAPENDA Nadine", "Discipline"],
        ["KAJIMB RIY AMALOL Viviane", "Discipline adjointe"],
        ["KAYIT TSHIBANGU NAOMIE", "Communication"],
        ["LAURA NDIJI KASY", "Communication adjointe"],
        ["NYOTA MUNABA Aline", "Secrétaire"],
        ["MALIKI NANU", "Secrétaire adjointe"],
        ["MAKIBIA KALUME Sylvie", "Diplomatie"],
        ["DEBORAH ILUNGA", "Diplomatie Adjoint"],
        ["LAURE KAYOWA", "Membre d'honneur"],
        ["EVODIE MUSAU", "Membre d'honneur adjointe"],
        ["MARIAM DIOP KAPEND", "Emploi, travail et prévoyance sociale"],
        ["KAPADI DIMWANGALA amisa", "Emploi, travail et prévoyance sociale Adjointe"],
        ["BANZA MONGA Lydia", "Emploi, travail et prévoyance sociale Adjoin"],
        ["BANZA MONGA Lydia", "Emploi, travail et prévoyance sociale Adjointe"],
        ["NGOY NTAMBO Louis", "Coordonnateur"],
        ["USENI NDOBOLO KAJAMA", "Coordonnateur adjoint en charge de recrutement"],
        ["MBUYU WA MUTOMBO Vincent de Paul", "Coordonnateur adjoint en charge d'organisation administration"],
        ["TSHIJIKA MUNENGE Joël", "Coordonnateur adjoint en charge de l'implantation"],
    ];
    public static $user4 = [
        ["LONGO LONGO Patrick", "Coordonnateur adjoint en charge de l'implantation"],
        ["NGOIE MUHEMBA Israël", "Coordonnateur adjoint en charge de suivi des organes de bases"],
        ["DIKENI MELEKA Hervé", "Rapporteur"],
        ["KARIL MACES Odette", "Reporteur adjointe"],
        ["TSHIBAMB NAWEJ Patient", "Rapporteur adjoint"],
        ["FEZA MUSAPANA Céline", "Trésorière"],
        ["JEAN VABAZI", "Chargé d' animation politique"],
        ["BYAMUNGU VATERANYA", "Chargé d' animation politique"],
        ["MANGIE OLANGI", "Chargé d' animation politique"],
        ["FRANCINE VAJURAMO", "Chargé d' animation politique"],
        ["MARIE KAJAMA", "Chargé d' animation politique"],
        ["MWEHU VALERY", "Communicateur"],
        ["LUDY KAMBAJ", "Communicatrice"],
        ["NYUNDO KALALA", "Communicateur"],
        ["BONIFACIO TSHIKOMBA Cavula", "Coordonnateur provincial de la cellule de mobilisation et propagande"],
        ["MUSENDEKA KAJ Gisèle", "Coordonnateur adjointe en charge de la zone DILALA I"],
        ["MASENGO WA KABUYA Patient", "Coordonnateur adjoint en charge de la zone DILALA 2"],
        ["NGOY WA MWIKA Isidore", "Coordonnateur adjoint en charge de la Zone MANIKA I"],
        ["NKULU WA NDALAMBA Esther", "Coordonnateur adjoint en charge de la Zone MANIKA 2"],
        ["KAZADI ULEMBA Michel", "Mobilisateur LATIN KISU"],
        ["NKULU WA NKULU Véronique", "Mobilisateur Cité MANIKA 1"],
        ["LUNDA NGANDU Guellord", "Mobilisateur Cité MANIKA 2"],
        ["KABWE MWAMBA FANA", "Mobilisateur Cité MANIKA 3"],
        ["KUMWIMBA KIFIKWA Elda", "Mobilisateur Cité DIUR I"],
        ["TSHINKAKU KAYEMBE Nicolas", "Mobilisateur Cité DIUR 2"],
        ["BAKANA NKANKU", "Mobilisateur bel air I"],
        ["MULUNGE MAKONGA Judith", "Mobilisateur bel air 2"],
        ["DJUMA BAKANA", "Mobilisateur Crète I"],
        ["NGOIE ILIJNGA Lolie", "Mobilisateur Crète 2"],
        ["KAZADI MBUYA Horis", "Mobilisateur KASULU KABILA"],
        ["MAYEMB NTAMBU Désiré", "Mobilisateur KASULU"],
        ["MASENGO MUKENA LILO", "Mobilisateur KAMANYOLA I"],
        ["PAGNWE MUSEWA Béatrice", "Mobilisateur KAMANYOLA 2"],
        ["YAV MBUMB Joel", "Mobilisateur KAMANYOLA 3"],
        ["KISHIKO MPOYO Mardochée", "Rapporteur"],
        ["KABUNDJI SANGO Clair", "Communication"],
        ["KANZ KASOMB Patient", "Photographe"],
    ];

    private static $categories_with_descriptions = [
        'Actualités Politiques' => 'Dernières nouvelles et analyses sur la politique nationale et internationale.',
        'Événements de l\'UNC' => 'Informations sur les événements organisés par l\'Union pour la Nation Congolaise.',
        'Propositions et Idées' => 'Espace pour partager et discuter des propositions et idées pour le développement de l\'association.',
        'Questions et Réponses' => 'Forum pour poser des questions et obtenir des réponses de la communauté.',
        'Annonces Officielles' => 'Communiqués et annonces officielles de l\'UNC.',
        'Projets de développement' => 'Détails et mises à jour sur les projets de développement en cours et futurs.',
        'Ressources et Documents' => 'Accès à des documents importants et des ressources utiles pour les membres.',
        'Présentation des Membres' => 'Profils et présentations des membres de l\'association.',
        'Aide et Assistance' => 'Support et assistance pour les membres ayant des questions ou des problèmes.',
        'Formation et Éducation' => 'Opportunités de formation et d\'éducation pour les membres.',
        'Relations Internationales' => 'Informations sur les relations et collaborations internationales de l\'UNC.',
        'Économie et social' => 'Discussions sur les questions économiques et sociales affectant la communauté.',
        'Santé et Bien-être' => 'Conseils et informations sur la santé et le bien-être des membres.',
        'Sport et Loisir' => 'Activités sportives et de loisirs pour les membres de l\'association.'
    ]; 
    
    public static $categories_with_pictures = [
        'Actualités Politiques' => 'politic-66ccefe58953f.jpg',
        'Événements de l\'UNC' => 'events-66ccf10515c12.jpg',
        'Propositions et Idées' => 'ideas-66ccf11d69903.jpg',
        'Questions et Réponses' => 'question-reponse-66ccf142e5db8.jpg',
        'Annonces Officielles' => 'annonces-66ccf158379ac.jpg',
        'Projets de développement' => 'projects-66ccf16b3103e.jpg',
        'Ressources et Documents' => 'documents-66ccf186af085.jpg',
        'Présentation des Membres' => 'new-member-66ccf1a065f41.jpg',
        'Aide et Assistance' => 'assistance-66ccf1b557876.jpg',
        'Formation et Éducation' => 'education-66ccf1c7540f4.jpg',
        'Relations Internationales' => 'rel-inter-66ccf1dc4ccf8.jpg',
        'Économie et social' => 'ess-66ccf1f3e6730.jpg',
        'Santé et Bien-être' => 'sante-bien-ettre-66ccf20adef89.jpg',
        'Sport et Loisir' => 'sport-loisir-66ccf21e39253.jpg'
    ];
    
    private static $fonctionsForMembership = [
        'Secrétaire Interfédéral'=>'sif',
        'Secrétaire interfédéral Adjoint'=>'SIFA',
        'Président interfédéral des jeunes' =>'pdt jeunes',
        'Présidente interfédérale des femmes'=>'pdt femmes', 
        'Secrétaire exécutif ubain'=>'secexurbain', 
        'Président Fédéral'=>'pdt federal', 
    ];

    private static $levels = [
        'National' => 'National',
        'Interfédéral' => 'Interfédéral',
        'Fédéral' => 'Fédéral',
    ];

    private static $functions = [
        'Interfédération provincial' => [
            'Président de l\'interfédération' => 'Président de l\'interfédération',
            'Vice président' => 'Vice président',
            // Ajoutez d'autres fonctions ici
        ],
        'Interfédération provincial des femmes' => [
            'Inspecteur provincial' => 'Inspecteur provincial',
            'Inspecteur provincial adjoint' => 'Inspecteur provincial adjoint',
            'Questeur' => 'Questeur',
            // Ajoutez d'autres fonctions ici
        ],
        'Interfédération provincial des jeunes' => [
            'Président de l\'interfédération' => 'Président de l\'interfédération',
            'Vice président' => 'Vice président',
            // Ajoutez d'autres fonctions ici
        ],
        'Inspection provincial' => [
            'Inspecteur provincial' => 'Inspecteur provincial',
            'Inspecteur provincial adjoint' => 'Inspecteur provincial adjoint',
            'Questeur' => 'Questeur',
            // Ajoutez d'autres fonctions ici
        ],
        'Coordination provincial' => [
            'Président de l\'interfédération' => 'Président de l\'interfédération',
            'Vice président' => 'Vice président',
            // Ajoutez d'autres fonctions ici
        ],
    ];

}

