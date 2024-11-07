import { Component, ElementRef, ViewChild } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';

@Component({
  selector: 'app-training-vmf2',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent
  ],
  templateUrl: './training-vmf2.component.html',
  styleUrl: './training-vmf2.component.css'
})
export class TrainingVmf2Component {
  @ViewChild('training') trainingDiv!: ElementRef;

  scrollToTraining() {
    const element = this.trainingDiv.nativeElement;
    const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
    window.scrollTo({ top: top, behavior: 'smooth' });
  }

}
