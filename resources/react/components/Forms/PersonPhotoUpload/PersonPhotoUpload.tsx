import { useEffect, useId, useRef, useState, type DragEvent } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import PersonPhotoCamera from './PersonPhotoCamera';
import { mirrorPhoto } from './mirrorPhoto';
import type { PersonPhotoUploadProps } from './PersonPhotoUpload.types';
import { defaultPhotoTypes, photoTypeLabels, usePersonPhoto } from './usePersonPhoto';
import './PersonPhotoUpload.css';

export default function PersonPhotoUpload({
  id, name = 'photo', label = 'Foto', current, file, removed = false, disabled = false,
  error, size = '11rem', acceptedTypes = defaultPhotoTypes, maxSizeBytes = 2 * 1024 * 1024,
  allowCamera = true, onChange, onRemove, onError, onValidatingChange,
}: PersonPhotoUploadProps): React.JSX.Element {
  const generatedId = useId();
  const inputId = id ?? `person-photo-${generatedId}`;
  const input = useRef<HTMLInputElement>(null);
  const selectButton = useRef<HTMLButtonElement>(null);
  const cameraInput = useRef<HTMLInputElement>(null);
  const dragDepth = useRef(0);
  const [dragging, setDragging] = useState(false);
  const [cameraOpen, setCameraOpen] = useState(false);
  const captureType = acceptedTypes.includes('image/jpeg') ? 'image/jpeg' : acceptedTypes[0] ?? 'image/jpeg';
  const { preview, validating, selectPhoto, cancelPending } = usePersonPhoto({ file, disabled, acceptedTypes, maxSizeBytes, onChange, onError, onValidatingChange });
  const src = preview ?? (!removed ? current : undefined);
  const description = [`${inputId}-help`, error ? `${inputId}-error` : undefined].filter(Boolean).join(' ');
  const photoLabel = label.toLocaleLowerCase('pt-BR');

  useEffect(() => { if (disabled) { dragDepth.current = 0; setDragging(false); } }, [disabled]);
  function hasFiles(event: DragEvent) { return Array.from(event.dataTransfer.types).includes('Files'); }
  function clearDrag() { dragDepth.current = 0; setDragging(false); }
  // Só a foto nova (ainda não salva) pode ser espelhada: a atual é apenas uma URL.
  async function mirrorSelected() {
    if (!file) return;
    try { selectPhoto(await mirrorPhoto(file)); }
    catch { onError('Não foi possível espelhar a foto. Tente novamente.'); }
  }
  // Celular: câmera nativa pelo input com `capture`. Computador: webcam em janela (exige HTTPS ou localhost).
  function openCamera() {
    if (window.matchMedia('(pointer: coarse)').matches) { cameraInput.current?.click(); return; }
    if (window.isSecureContext && typeof navigator.mediaDevices?.getUserMedia === 'function') { setCameraOpen(true); return; }
    onError('A câmera não está disponível neste navegador ou conexão (é necessário HTTPS). Selecione uma imagem.');
  }

  return <div className="personPhotoUpload">
    <label htmlFor={inputId} className="personPhotoUpload__label">{label}</label>
    <div className="personPhotoUpload__wrapper" style={{ width: size }}>
      <button ref={selectButton} type="button" disabled={disabled}
        className={joinClassNames(
          'personPhotoUpload__preview',
          dragging ? 'personPhotoUpload__preview--dragging' : undefined,
          error ? 'personPhotoUpload__preview--invalid' : undefined,
        )}
        aria-label={`Selecionar ${photoLabel}`} aria-describedby={description} aria-invalid={!!error}
        onClick={() => input.current?.click()}
        onDragEnter={event => {
          if (!hasFiles(event)) return;
          event.preventDefault();
          if (!disabled) { dragDepth.current += 1; setDragging(true); }
        }}
        onDragOver={event => {
          if (!hasFiles(event)) return;
          event.preventDefault();
          event.dataTransfer.dropEffect = disabled ? 'none' : 'copy';
        }}
        onDragLeave={event => {
          event.preventDefault();
          dragDepth.current = Math.max(0, dragDepth.current - 1);
          if (!dragDepth.current) setDragging(false);
        }}
        onDragEnd={clearDrag}
        onDrop={event => {
          event.preventDefault();
          clearDrag();
          if (disabled) return;
          const files = event.dataTransfer.files;
          if (files.length > 1) { onError('Selecione uma foto por vez.'); return; }
          if (files[0]) selectPhoto(files[0]);
        }}>
        {src ? <img src={src} className="personPhotoUpload__image" alt={`Prévia de ${photoLabel}`} draggable={false} />
          : <span className="personPhotoUpload__empty"><i className="ti-camera" aria-hidden="true" /><span>ADICIONAR<br />FOTO</span></span>}
      </button>
      {src ? <button type="button" className="personPhotoUpload__remove" disabled={disabled} aria-label={`Remover ${photoLabel}`}
        onClick={() => {
          cancelPending();
          if (input.current) input.current.value = '';
          onRemove();
          selectButton.current?.focus();
        }}><i className="ti-trash" aria-hidden="true" /></button> : null}
      {file && preview ? <button type="button" className="personPhotoUpload__mirror" disabled={disabled || validating}
        aria-label={`Espelhar ${photoLabel}`} title={`Espelhar ${photoLabel}`}
        onClick={() => void mirrorSelected()}><i className="fa fa-arrows-h" aria-hidden="true" /></button> : null}
      {allowCamera ? <button type="button" className="personPhotoUpload__camera" disabled={disabled}
        aria-label={`Tirar ${photoLabel} com a câmera`} title={`Tirar ${photoLabel} com a câmera`}
        onClick={openCamera}><i className="ti-camera" aria-hidden="true" /></button> : null}
    </div>
    <input ref={input} id={inputId} type="file" name={name} hidden disabled={disabled}
      accept={acceptedTypes.join(',')} aria-describedby={description}
      onChange={event => {
        const selected = event.target.files?.[0];
        event.target.value = '';
        if (selected) selectPhoto(selected);
      }} />
    {allowCamera ? <input ref={cameraInput} type="file" hidden disabled={disabled} tabIndex={-1}
      accept={captureType} capture="user" aria-hidden="true"
      onChange={event => {
        const selected = event.target.files?.[0];
        event.target.value = '';
        if (selected) selectPhoto(selected);
      }} /> : null}
    {allowCamera ? <PersonPhotoCamera open={cameraOpen} label={label} type={captureType}
      onCapture={selectPhoto} onError={onError} onClose={() => setCameraOpen(false)} /> : null}
    <div id={`${inputId}-help`} className="personPhotoUpload__help">
      <small>{allowCamera
        ? 'Clique no círculo para selecionar, arraste uma imagem ou use a câmera'
        : 'Clique no círculo para selecionar ou arraste uma imagem'}</small>
      <small>O círculo mostra a parte central da imagem que aparecerá na foto.</small>
      <small>{acceptedTypes.map(type => photoTypeLabels[type]).join(', ')}. Máx {maxSizeBytes / (1024 * 1024)}MB.</small>
    </div>
    <span className="personPhotoUpload__dragStatus" role="status">{validating ? 'Preparando a foto selecionada...' : dragging ? 'Solte a imagem para selecionar a foto.' : ''}</span>
    {removed && current ? <p className="personPhotoUpload__help">A foto será removida ao salvar.</p> : null}
    {error ? <p id={`${inputId}-error`} className="personPhotoUpload__error" role="alert">{error}</p> : null}
    <div className="personPhotoUpload__divider" />
  </div>;
}
