import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { HomeComponent } from '../home/home.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-about',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            HomeComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            CommonModule,
            ScrollButtonComponent
  ],
  templateUrl: './about.component.html',
  styleUrl: './about.component.css'
})
export class AboutComponent {
  isContentVisible: boolean = false; 
  buttonText: string = 'Read More';

  toggleContent() {
     this.isContentVisible = !this.isContentVisible; 
     this.buttonText = this.isContentVisible ? 'Read Less' : 'Read More';
  } 
}
