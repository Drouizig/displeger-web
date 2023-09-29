<?php

return [
    'page_title' => [
        'dashboard' => 'Taolenn stur',
        'detail' => '%entity_as_string%',
        'edit' => 'Embann %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Krouiñ "%entity_label_singular%"',
        'exception' => 'Fazi|Fazioù',
    ],

    'datagrid' => [
        'hidden_results' => 'Certains résultats ne peuvent pas être affichés car vous n\'avez pas la permission',
        'no_results' => 'N\eus bet kavet netra',
    ],

    'paginator' => [
        'first' => 'Kentañ',
        'previous' => 'Kent',
        'next' => 'Da-heul',
        'last' => 'Diwezhañ',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> sur <strong>%results%</strong>',
        'results' => '{0} Aucun résultat|{1} <strong>1</strong> résultat|]1,Inf] <strong>%count%</strong> résultats',
    ],

    'label' => [
        'true' => 'Ya',
        'false' => 'Ket',
        'empty' => 'Goullo',
        'null' => 'Hini ebet',
        'nullable_field' => 'Leuskel goullo',
        'object' => 'Objed PHP',
        'inaccessible' => 'Inaccessible',
        'inaccessible.explanation' => 'Aucun accesseur n\'existe pour cette propriété ou celle-ci n\'est pas publique.',
        'form.empty_value' => 'Aucun(e)',
    ],

    'field' => [
        'code_editor.view_code' => 'Voir le code',
        'text_editor.view_content' => 'Voir le contenu',
    ],

    'action' => [
        'entity_actions' => 'Gweredoù',
        'new' => 'Krouiñ %entity_label_singular%',
        'search' => 'Klask',
        'detail' => 'Gwelet',
        'edit' => 'Embann',
        'delete' => 'Dilemel',
        'cancel' => 'Nullañ',
        'index' => 'Distreiñ er roll',
        'deselect' => 'Diziuzañ',
        'add_new_item' => 'Ouzhpennañ un elfenn nevez',
        'remove_item' => 'Dilemel an elfenn',
        'choose_file' => 'Dibab ur restr',
        'close' => 'Serriñ',
        'create' => 'Krouiñ',
        'create_and_add_another' => 'Krouiñ hag ouzhpennañ',
        'create_and_continue' => 'Krouiñ ha kenderc’hel da gemmañ',
        'save' => 'Enrollañ ar c’hemmoù',
        'save_and_continue' => 'Enrollañ ha kenderc’hel da gemmañ',
    ],

    'batch_action_modal' => [
        'title' => 'Vous allez appliquer l\'action "%action_name%" à %num_items% élément(s).',
        'content' => 'Cette action est irréversible.',
        'action' => 'Procéder',
    ],

    'delete_modal' => [
        'title' => 'Sur oc’h e fell deoc’h dilemel an elfenn-mañ ?',
        'content' => 'Ne vo ket posupl dizober.',
    ],

    'filter' => [
        'title' => 'Siloù',
        'button.clear' => 'Effacer',
        'button.apply' => 'Appliquer',
        'label.is_equal_to' => 'est égal(e) à',
        'label.is_not_equal_to' => 'est différent(e) de',
        'label.is_greater_than' => 'est supérieur(e) à',
        'label.is_greater_than_or_equal_to' => 'est supérieur(e) ou égal(e) à',
        'label.is_less_than' => 'est inférieur(e) à',
        'label.is_less_than_or_equal_to' => 'est inférieur(e) ou égal(e) à',
        'label.is_between' => 'est entre',
        'label.contains' => 'contient',
        'label.not_contains' => 'ne contient pas',
        'label.starts_with' => 'commence par',
        'label.ends_with' => 'finit par',
        'label.exactly' => 'est strictement égal(e) à',
        'label.not_exactly' => 'est strictement différent(e) de',
        'label.is_same' => 'est',
        'label.is_not_same' => 'n\'est pas',
        'label.is_after' => 'est postérieure à',
        'label.is_after_or_same' => 'est postérieure à ou est le',
        'label.is_before' => 'est antérieure à',
        'label.is_before_or_same' => 'est antérieure à ou est le',
    ],

    'form' => [
        'are_you_sure' => 'Vous n\'avez pas sauvegardé vos modifications.',
        'tab.error_badge_title' => '1 champ invalide|%count% champs invalides',
        'slug.confirm_text' => 'Si vous modifiez le slug, vous pouvez casser des liens sur d\'autres pages.',
    ],

    'user' => [
        'logged_in_as' => 'Connecté en tant que',
        'unnamed' => 'Utilisateur sans nom',
        'anonymous' => 'Utilisateur anonyme',
        'sign_out' => 'Digennaskañ',
        'exit_impersonation' => 'Arrêter l\'impersonnalisation',
    ],

    'login_page' => [
        'username' => 'Anv arveriad',
        'password' => 'Ger-tremen',
        'sign_in' => 'Connectez-vous',
        'forgot_password' => 'Mot de passe oublié ?',
        'remember_me' => 'Rester connecté',
    ],

    'exception' => [
        'entity_not_found' => 'Cet élément n\'est plus disponible.',
        'entity_remove' => 'Cet élément ne peut être supprimé car d\'autres éléments en dépendent.',
        'forbidden_action' => 'L\'action demandée ne peut être exécutée sur cet élément.',
        'insufficient_entity_permission' => 'Vous n\'êtes pas autorisé à accéder à cet élément.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Aucun résultat trouvé',
        'no-more-results' => 'Aucun autre résultat trouvé',
        'loading-more-results' => 'Chargement de résultats supplémentaires…',
    ],
];
