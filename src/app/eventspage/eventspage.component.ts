import { Component, ElementRef, ViewChild } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-eventspage',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            ScrollButtonComponent
  ],
  templateUrl: './eventspage.component.html',
  styleUrl: './eventspage.component.css'
})
export class EventspageComponent { 
  @ViewChild('training') trainingDiv!: ElementRef;

  scrollToTraining() {
    const element = this.trainingDiv.nativeElement;
    const top = element.getBoundingClientRect().top + window.pageYOffset - 100; // Adjust the offset as needed
    window.scrollTo({ top: top, behavior: 'smooth' });
  }

}
