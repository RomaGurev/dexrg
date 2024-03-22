<? 
/*
Файл, подключаемый template.php.
> содержит разметку дополнений для страницы.
*/
?>

<!-- Всплывающее сообщение -->
<div class="container toast-container position-fixed bottom-0 end-0 p-3 mb-5">
  <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <svg width="18" height="18" class="me-2">
        <image xlink:href="/images/icons/info-circle.svg" width="18" height="18" />
      </svg>
      <strong class="me-auto">Сообщение</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      Тестовое сообщение
    </div>
  </div>
</div>

<!-- Модальное окно -->
<div class="modal fade" id="RGModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header" style="padding: 0.5rem 1rem">
        <h5 class="modal-title display-6 fs-4" id="modalLabel">Учетная карта призывника</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body lead">
        <div id="modalContent">{содержимое окна}</div>
      </div>
    </div>
  </div>
</div>