<?php

function template_preprocess_card(array &$variables) {

  // Helpful $content variable for templates.
  foreach (\Drupal\Core\Render\Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}