import { Component, ElementRef, ViewChild } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';

@Component({
  selector: 'app-eventspage-sva',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
  ],
  templateUrl: './eventspage-sva.component.html',
  styleUrl: './eventspage-sva.component.css'
})
export class EventspageSvaComponent {
  @ViewChild('training') trainingDiv!: ElementRef;

  scrollToTraining() {
    const element = this.trainingDiv.nativeElement;
    const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
    window.scrollTo({ top: top, behavior: 'smooth' });
  }
}
