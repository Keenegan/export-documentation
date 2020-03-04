<?php

namespace Drupal\documentation_export\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\documentation_export\DocumentationExport;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Dompdf\Dompdf;

/**
 * Class DocumentationExportController.
 *
 * @package Drupal\documentation_export\Controller
 */
class DocumentationExportController extends ControllerBase {

  /**
   * The documentationExport service.
   *
   * @var \Drupal\documentation_export\DocumentationExport
   */
  protected $documentationExport;

  /**
   * {@inheritdoc}
   */
  public function __construct(DocumentationExport $documentationExport) {
    $this->documentationExport = $documentationExport;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('documentation_export.service')
    );
  }

  /**
   * Display the entities documentation list.
   *
   * @return array
   *   The render array used to display the list.
   */
  public function exportEntities() {
    return [
      'dblog_table' => [
        '#theme' => 'documentation_export',
        '#data' => $this->documentationExport->exportDocumentation(),
      ],
    ];
  }

  public function printPdf() {
    $renderable = $this->exportEntities();
    $rendered = \Drupal::service('renderer')->render($renderable);
    $dompdf = new Dompdf();
    $dompdf->loadHtml($rendered);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream();
    return ['#markup' => ''];
  }

}
