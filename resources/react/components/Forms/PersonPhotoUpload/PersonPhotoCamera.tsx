import { useEffect, useRef, useState } from 'react';
import Button from '@/components/UI/Button/Button';
import Modal from '@/components/UI/Modal/Modal';
import { mirrorPhoto } from './mirrorPhoto';
import type { PersonPhotoType } from './PersonPhotoUpload.types';

const MAX_WIDTH = 1280;
const MAX_SIZE = 1024;

interface PersonPhotoCameraProps {
  open: boolean;
  label: string;
  type: PersonPhotoType;
  onCapture: (file: File) => void;
  onError: (message: string) => void;
  onClose: () => void;
}

function cameraErrorMessage(error: unknown): string {
  const name = error instanceof DOMException ? error.name : '';

  if (name === 'NotAllowedError' || name === 'SecurityError') {
    return 'O acesso à câmera foi negado. Libere a permissão no navegador ou selecione uma imagem.';
  }

  if (name === 'NotFoundError' || name === 'OverconstrainedError') {
    return 'Nenhuma câmera foi encontrada neste dispositivo. Selecione uma imagem.';
  }

  if (name === 'NotReadableError') {
    return 'A câmera está em uso por outro aplicativo. Feche-o e tente novamente.';
  }

  return 'Não foi possível abrir a câmera. Selecione uma imagem.';
}

/**
 * Captura pela webcam (getUserMedia) para computadores. Em celulares, o
 * componente pai abre a câmera nativa pelo input com `capture`.
 */
export default function PersonPhotoCamera({ open, label, type, onCapture, onError, onClose }: PersonPhotoCameraProps): React.JSX.Element | null {
  const video = useRef<HTMLVideoElement>(null);
  const stream = useRef<MediaStream | null>(null);
  const [facingMode, setFacingMode] = useState<'user' | 'environment'>('user');
  // A foto é salva como aparece na tela; selfies começam espelhadas.
  const [mirrored, setMirrored] = useState(true);
  const [saving, setSaving] = useState(false);
  const [canSwitch, setCanSwitch] = useState(false);
  const [ready, setReady] = useState(false);
  const [captured, setCaptured] = useState<{ file: File; url: string } | null>(null);
  const onErrorRef = useRef(onError);
  const onCloseRef = useRef(onClose);
  onErrorRef.current = onError;
  onCloseRef.current = onClose;

  function stopStream(): void {
    stream.current?.getTracks().forEach(track => track.stop());
    stream.current = null;
    setReady(false);
  }

  useEffect(() => {
    if (!open || captured) return undefined;

    let cancelled = false;

    async function start(): Promise<void> {
      try {
        const media = await navigator.mediaDevices.getUserMedia({
          video: { facingMode, width: { ideal: MAX_WIDTH } },
          audio: false,
        });

        if (cancelled) {
          media.getTracks().forEach(track => track.stop());
          return;
        }

        stream.current = media;

        if (video.current) {
          video.current.srcObject = media;
          await video.current.play().catch(() => undefined);
        }

        setReady(true);

        const devices = await navigator.mediaDevices.enumerateDevices().catch(() => []);
        if (!cancelled) setCanSwitch(devices.filter(device => device.kind === 'videoinput').length > 1);
      } catch (error) {
        if (cancelled) return;
        onErrorRef.current(cameraErrorMessage(error));
        onCloseRef.current();
      }
    }

    void start();

    return () => {
      cancelled = true;
      stopStream();
    };
  }, [open, facingMode, captured]);

  useEffect(() => () => {
    if (captured) URL.revokeObjectURL(captured.url);
  }, [captured]);

  function close(): void {
    stopStream();
    setCaptured(null);
    setSaving(false);
    onClose();
  }

  async function confirmPhoto(file: File): Promise<void> {
    setSaving(true);

    try {
      onCapture(mirrored ? await mirrorPhoto(file) : file);
      close();
    } catch {
      setSaving(false);
      onError('Não foi possível espelhar a foto. Tente novamente.');
    }
  }

  function capture(): void {
    const element = video.current;

    if (!element || !element.videoWidth) return;

    // Recorte quadrado central, o mesmo exibido no palco e usado nos avatares redondos.
    const side = Math.min(element.videoWidth, element.videoHeight);
    const output = Math.min(side, MAX_SIZE);
    const canvas = document.createElement('canvas');
    canvas.width = output;
    canvas.height = output;
    canvas.getContext('2d')?.drawImage(
      element,
      (element.videoWidth - side) / 2,
      (element.videoHeight - side) / 2,
      side,
      side,
      0,
      0,
      output,
      output,
    );
    canvas.toBlob(blob => {
      if (!blob) {
        onError('Não foi possível capturar a foto. Tente novamente.');
        return;
      }

      const extension = type === 'image/png' ? 'png' : type === 'image/webp' ? 'webp' : 'jpg';
      const file = new File([blob], `foto-camera-${Date.now()}.${extension}`, { type });

      stopStream();
      setCaptured({ file, url: URL.createObjectURL(file) });
    }, type, 0.9);
  }

  if (!open) return null;

  return (
    <Modal
      open={open}
      title={`Tirar ${label.toLocaleLowerCase('pt-BR')}`}
      effect="fadein"
      contentClassName="personPhotoCamera"
      onClose={close}
      footer={(
        <div className="personPhotoCamera__actions">
          <Button
            tone="cancel"
            label="Cancelar"
            title="Cancelar e fechar a câmera"
            aria-label="Cancelar e fechar a câmera"
            onClick={close}
          />
          <Button
            tone="dark-blue"
            leftIconClass="fa fa-arrows-h"
            label={mirrored ? 'Desfazer espelho' : 'Espelhar'}
            title={mirrored ? 'Desfazer o espelhamento da foto' : 'Espelhar a foto'}
            disabled={saving || (!captured && !ready)}
            onClick={() => setMirrored(value => !value)}
          />
          {captured ? (
            <>
              <Button
                tone="dark-blue"
                leftIconClass="fa fa-refresh"
                label="Tirar outra"
                title="Descartar e tirar outra foto"
                onClick={() => setCaptured(null)}
              />
              <Button
                tone="save"
                leftIconClass="fa fa-check"
                label={saving ? 'Preparando...' : 'Usar foto'}
                title="Usar a foto capturada"
                disabled={saving}
                onClick={() => void confirmPhoto(captured.file)}
              />
            </>
          ) : (
            <>
              {canSwitch ? (
                <Button
                  tone="dark-blue"
                  leftIconClass="fa fa-exchange"
                  label="Trocar câmera"
                  title="Alternar entre as câmeras do dispositivo"
                  disabled={!ready}
                  onClick={() => {
                    setMirrored(facingMode !== 'user');
                    setFacingMode(mode => (mode === 'user' ? 'environment' : 'user'));
                  }}
                />
              ) : null}
              <Button
                tone="save"
                leftIconClass="fa fa-camera"
                label="Capturar"
                title="Capturar a foto"
                disabled={!ready}
                onClick={capture}
              />
            </>
          )}
        </div>
      )}
    >
      <div className="personPhotoCamera__stage">
        {captured ? (
          <img
            src={captured.url}
            className={`personPhotoCamera__media${mirrored ? ' personPhotoCamera__media--mirrored' : ''}`}
            alt="Foto capturada"
          />
        ) : (
          <video
            ref={video}
            className={`personPhotoCamera__media${mirrored ? ' personPhotoCamera__media--mirrored' : ''}`}
            autoPlay
            muted
            playsInline
            aria-label="Imagem ao vivo da câmera"
          />
        )}
        <span className="personPhotoCamera__guide" aria-hidden="true" />
        <span className="personPhotoCamera__status" role="status">
          {captured ? 'Foto capturada. Confira antes de usar.' : ready ? 'Centralize o rosto no círculo.' : 'Abrindo a câmera...'}
        </span>
      </div>
    </Modal>
  );
}
