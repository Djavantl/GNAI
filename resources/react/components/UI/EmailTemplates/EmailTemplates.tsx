import CardBox from "@/components/UI/CardBox/CardBox";
import ImageDisplay from "@/components/UI/ImageDisplay/ImageDisplay";
import { emailTemplates } from "./EmailTemplates.data";
import "./EmailTemplates.css";
export default function EmailTemplates(): React.JSX.Element {
  return (
    <CardBox>
      <div className="emailTemplates">
        {emailTemplates.map((item) => (
          <section key={item.href}>
            <h4>
              <b>{item.title}</b>
            </h4>
            <a href={item.href} target="_blank" rel="noopener noreferrer">
              <ImageDisplay src={item.src} alt={item.title} />
            </a>
          </section>
        ))}
      </div>
    </CardBox>
  );
}
