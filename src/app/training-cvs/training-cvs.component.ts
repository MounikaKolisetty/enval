import { Component, ElementRef, ViewChild } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';

@Component({
  selector: 'app-training-cvs',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent
  ],
  templateUrl: './training-cvs.component.html',
  styleUrl: './training-cvs.component.css'
})
export class TrainingCvsComponent {
  @ViewChild('training') trainingDiv!: ElementRef;

  scrollToTraining() {
    const element = this.trainingDiv.nativeElement;
    const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
    window.scrollTo({ top: top, behavior: 'smooth' });
  }
}
