import { useRef, useState } from 'react';
import FormPage from '@/components/Forms/FormPage/FormPage';
import CardBox from '@/components/UI/CardBox/CardBox';
import Button from '@/components/UI/Button/Button';
import TextInput from '@/components/Forms/TextInput/TextInput';
import ImageCropper from '@/components/Forms/ImageCropper/ImageCropper';
import type {
  CropData,
  ImageCropperHandle,
} from '@/components/Forms/ImageCropper/ImageCropper.types';
import { cropActions, initialCrop } from './ImageCropPage.data';
import './ImageCropPage.css';

export default function ImageCropPage(): React.JSX.Element {
  const cropper = useRef<ImageCropperHandle>(null);
  const [data, setData] = useState<CropData>({ ...initialCrop, rotate: 0 });
  const [cropJson, setCropJson] = useState('');
  const [imageJson, setImageJson] = useState('');
  const [source, setSource] = useState('assets/images/big/img4.jpg');
  const [ratio, setRatio] = useState('auto');
  const [url, setUrl] = useState('');
  const command = (method: string, argument?: unknown): unknown =>
    cropper.current?.command(method, argument);
  function exportImage(type?: string): void {
    const value = command('getDataURL', type);
    if (typeof value === 'string') setUrl(value);
  }
  return (
    <FormPage title="Recorte de imagem">
      <div className="imageCropPage">
        <div>
          <CardBox>
            <h4 className="imageCropPage__heading">Recorte</h4>
            <ImageCropper
              ref={cropper}
              src="/assets/react/images/big/img1.jpg"
              initialData={initialCrop}
              preview=".imageCropPage .preview"
              onChange={setData}
            />
            <hr />
            <ul className="imageCropPage__buttons">
              {cropActions.map((action) => (
                <li key={action.id} data-action={action.id}>
                  <Button
                    tone={action.tone}
                    onClick={() => command(action.method, action.value)}
                  >
                    {action.label}
                  </Button>
                </li>
              ))}
              <li data-action="setData">
                <Button tone="primary" onClick={() => command('setData', data)}>
                  Aplicar dados
                </Button>
              </li>
            </ul>
          </CardBox>
        </div>
        <div>
          <CardBox>
            <h4 className="imageCropPage__heading">Prévia</h4>
            <div className="imageCropPage__previews">
              <div className="preview preview-md" />
              <div className="preview preview-sm" />
              <div className="preview preview-xs" />
            </div>
          </CardBox>
          <CardBox>
            <h4 className="imageCropPage__heading">Valores atuais</h4>
            <div className="imageCropPage__values">
              {(
                [
                  ['x', 'X'],
                  ['y', 'Y'],
                  ['width', 'L'],
                  ['height', 'A'],
                  ['rotate', 'Rotação'],
                ] as const
              ).map(([key, label]) => (
                <label className="imageCropPage__group" key={key}>
                  <span>{label}</span>
                  <TextInput
                    aria-label={label}
                    value={data[key] ?? ''}
                    onChange={(e) =>
                      setData({
                        ...data,
                        [key]:
                          e.target.value === '' ? '' : Number(e.target.value),
                      })
                    }
                  />
                </label>
              ))}
            </div>
          </CardBox>
          <CardBox>
            <h4 className="imageCropPage__heading">Ações</h4>
            <div className="imageCropPage__group">
              <Button
                tone="info"
                onClick={() =>
                  setCropJson(JSON.stringify(command('getData')) ?? '')
                }
              >
                Obter dados
              </Button>
              <TextInput
                aria-label="Dados do recorte"
                value={cropJson}
                onChange={(e) => setCropJson(e.target.value)}
              />
            </div>
            <div className="imageCropPage__group">
              <Button
                tone="info"
                onClick={() =>
                  setImageJson(JSON.stringify(command('getImageData')) ?? '')
                }
              >
                Dados da imagem
              </Button>
              <TextInput
                aria-label="Dados da imagem"
                value={imageJson}
                onChange={(e) => setImageJson(e.target.value)}
              />
            </div>
            <div className="imageCropPage__group">
              <Button tone="primary" onClick={() => command('replace', source)}>
                Substituir
              </Button>
              <TextInput
                aria-label="Imagem substituta"
                value={source}
                onChange={(e) => setSource(e.target.value)}
              />
            </div>
            <div className="imageCropPage__group">
              <Button
                tone="primary"
                onClick={() => command('setAspectRatio', ratio)}
              >
                Proporção
              </Button>
              <TextInput
                aria-label="Proporção"
                value={ratio}
                onChange={(e) => setRatio(e.target.value)}
              />
            </div>
            <div className="imageCropPage__exports">
              <Button block tone="primary" onClick={() => exportImage()}>
                Obter URL
              </Button>
              <Button
                block
                tone="primary"
                onClick={() => exportImage('image/jpeg')}
              >
                Obter URL (JPG)
              </Button>
            </div>
            <textarea
              className="imageCropPage__url"
              aria-label="URL da imagem recortada"
              rows={3}
              value={url}
              onChange={(e) => setUrl(e.target.value)}
            />
            <div className="imageCropPage__result">
              {url && <img src={url} alt="Resultado do recorte" />}
            </div>
          </CardBox>
        </div>
      </div>
    </FormPage>
  );
}
