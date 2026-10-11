import {
  DefaultMediaObjects,
  ImagesPageTitle,
  MediaAlignmentObjects,
  MediaCard,
  MediaListObjects,
  ResponsiveAndThumbnailCard,
  ShapeAndSizeCard
} from './Images.sections';
import './Images.css';

export default function Images(): React.JSX.Element {
  return (
    <section className="imagesPage" aria-label="Imagens">
      <ImagesPageTitle />
      <ShapeAndSizeCard />
      <ResponsiveAndThumbnailCard />
      <MediaCard
        title="Objeto de mídia padrão"
        description="A mídia padrão exibe um objeto de mídia (imagens, vídeo, áudio) à esquerda ou à direita de um bloco de conteúdo."
      >
        <DefaultMediaObjects />
      </MediaCard>
      <MediaCard
        title="Alinhamento de mídia"
        description="Imagens ou outras mídias podem ser alinhadas ao topo, ao meio ou à base. O padrão é alinhado ao topo."
      >
        <MediaAlignmentObjects />
      </MediaCard>
      <MediaCard
        title="Lista de mídia"
        description="Com um pouco de marcação extra, você pode usar mídia dentro de listas, útil para comentários encadeados ou listas de artigos."
      >
        <MediaListObjects />
      </MediaCard>
    </section>
  );
}
