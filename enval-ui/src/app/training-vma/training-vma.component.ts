import { Component, ElementRef, ViewChild } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-training-vma',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            ScrollButtonComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
  ],
  templateUrl: './training-vma.component.html',
  styleUrl: './training-vma.component.css'
})
export class TrainingVmaComponent {
  @ViewChild('training') trainingDiv!: ElementRef;

  scrollToTraining() {
    const element = this.trainingDiv.nativeElement;
    const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
    window.scrollTo({ top: top, behavior: 'smooth' });
  }

}
