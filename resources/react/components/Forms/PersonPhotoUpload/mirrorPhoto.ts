/**
 * Gera uma cópia da imagem invertida horizontalmente, mantendo o formato original.
 */
export async function mirrorPhoto(file: File): Promise<File> {
  const bitmap = await createImageBitmap(file);
  const canvas = document.createElement('canvas');
  canvas.width = bitmap.width;
  canvas.height = bitmap.height;

  const context = canvas.getContext('2d');
  if (!context) throw new Error('Canvas indisponível.');

  context.translate(canvas.width, 0);
  context.scale(-1, 1);
  context.drawImage(bitmap, 0, 0);
  bitmap.close();

  const blob = await new Promise<Blob | null>(resolve => canvas.toBlob(resolve, file.type, 0.92));
  if (!blob) throw new Error('Não foi possível gerar a imagem espelhada.');

  return new File([blob], file.name, { type: file.type, lastModified: Date.now() });
}
