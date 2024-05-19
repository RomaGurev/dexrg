<? 
/*
Файл, подключаемый template.php.
> содержит разметку дополнений для страницы.
*/
?>

<!-- Контейнер для всплывающих сообщений showToast -->
<div id="toastContainer" class="container toast-container position-fixed bottom-0 start-0 mb-5"></div>

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

<!-- Модальное окно подтверждения действия-->
<div class="modal fade" id="areYouSureModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="font-family: 'Segoe UI';">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title display-6 fs-4" id="modalLabel">Вы уверены?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body lead">
        <div id="areYouSureModalContent">{содержимое окна}</div>
      </div>
      <div class="modal-footer">
        <button type="button" id="areYouSureModalClose" class="btn btn-secondary w-20" data-bs-dismiss="modal">Нет</button>
        <button type="button" id="areYouSureModalConfirm" class="btn btn-primary w-20">Да</button>
      </div>
    </div>
  </div>
</div>