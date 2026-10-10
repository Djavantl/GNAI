import CardBox from "@/Components/UI/CardBox/CardBox";
import ImageDisplay from "@/Components/UI/ImageDisplay/ImageDisplay";
import { emailTemplates } from "./EmailTemplates.data";
import "./EmailTemplates.css";
export default function EmailTemplates(): JSX.Element {
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
